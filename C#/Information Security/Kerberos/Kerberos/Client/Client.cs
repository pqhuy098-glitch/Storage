using System.Net;
using System.Net.Sockets;
using System.Text;
using System.Globalization;

public class Client
{
    private const string KDC_IP = "127.0.0.1";
    private const int KDC_PORT = 8888;

    private const string SERVER_IP = "127.0.0.1";
    private const int SERVER_PORT = 8899;

    private static readonly byte[] ClientMasterKey = Encoding.UTF8.GetBytes("AlicePassword123");

    public static void Main(string[] args)
    {
        string clientId = "Alice";

        Console.WriteLine("Sending AS_REQ to KDC");
        var (tgt, sessionKeyC_TGS) = CallAsReq(clientId);

        Console.WriteLine("Sending TGS_REQ to KDC");
        var (serviceTicket, sessionKeyC_Service) = CallTgsReq(tgt, sessionKeyC_TGS, clientId, "FileServer_Service");

        Console.WriteLine("Sending AP_REQ to Server (with Mutual Authentication AP_REP)");
        CallApReq(serviceTicket, sessionKeyC_Service, clientId);
    }

    private static string SendRequest(string ip, int port, string message)
    {
        TcpClient client = new TcpClient(ip, port);
        using NetworkStream stream = client.GetStream();
        using StreamReader reader = new StreamReader(stream);
        using StreamWriter writer = new StreamWriter(stream)
        {
            AutoFlush = true
        };

        writer.WriteLine(message);
        return reader.ReadLine() ?? "";
    }

    private static (string tgt, string sessionKey) CallAsReq(string clientId)
    {
        string response = SendRequest(KDC_IP, KDC_PORT, $"AS_REQ|{clientId}");
        string[] parts = response.Split('|');

        if (parts[0].StartsWith("ERROR"))
        {
            throw new Exception($"AS Error: {parts[1]}");
        }

        string encryptedSessionKey = parts[0];
        string tgt = parts[1];
        string sessionKeyC_TGS;
        try
        {
            sessionKeyC_TGS = DecryptText(encryptedSessionKey, ClientMasterKey);
        }
        catch
        {
            throw new Exception("AS Error: Invalid password or Master Key mismatch!");
        }

        Console.WriteLine($"[Client] Successfully received TGT & Random SessionKey_C_TGS.");
        return (tgt, sessionKeyC_TGS);
    }

    private static (string ticket, string sessionKey) CallTgsReq(string tgt, string sessionKeyC_TGS, string clientId, string service)
    {
        byte[] tgsKeyBytes = Encoding.UTF8.GetBytes(sessionKeyC_TGS);

        string authPlain = $"{clientId}|{DateTime.UtcNow.ToString("o")}";
        string authenticator = EncryptText(authPlain, tgsKeyBytes);

        string response = SendRequest(KDC_IP, KDC_PORT, $"TGS_REQ|{tgt}|{authenticator}|{service}");
        string[] parts = response.Split('|');

        if (parts[0].StartsWith("ERROR"))
        {
            throw new Exception($"TGS Error: {parts[1]}");
        }

        string encryptedSessionKeyService = parts[0];
        string serviceTicket = parts[1];

        string sessionKeyC_Service = DecryptText(encryptedSessionKeyService, tgsKeyBytes);
        Console.WriteLine($"[Client] Successfully received Service Ticket & Random SessionKey_C_Service.");
        return (serviceTicket, sessionKeyC_Service);
    }

    private static void CallApReq(string serviceTicket, string sessionKeyC_Service, string clientId)
    {
        byte[] serviceKeyBytes = Encoding.UTF8.GetBytes(sessionKeyC_Service);

        string authTime = DateTime.UtcNow.ToString("o");
        string authPlain = $"{clientId}|{authTime}";
        string authenticator = EncryptText(authPlain, serviceKeyBytes);

        string response = SendRequest(SERVER_IP, SERVER_PORT, $"AP_REQ|{serviceTicket}|{authenticator}");
        string[] parts = response.Split('|');

        if (parts[0] == "ACCESS_GRANTED" && parts.Length >= 2)
        {
            string encryptedApRep = parts[1];
            string decryptedApRep = DecryptText(encryptedApRep, serviceKeyBytes);
            string[] repParts = decryptedApRep.Split('|');

            if (repParts[0] == "OK")
            {
                Console.WriteLine("[Client] Mutual Authentication successful! Server verified.");
                Console.WriteLine("[Client] Access granted to FileServer.");
            }
            else
            {
                Console.WriteLine("[Client] AP_REP verification failed.");
            }
        }
        else
        {
            Console.WriteLine($"[Client] Authentication failed: {response}");
        }
    }

    private static string EncryptText(string plainText, byte[] key)
    {
        byte[] bytes = Encoding.UTF8.GetBytes(plainText);
        byte[] encrypted = AES.EncryptFile(bytes, key);
        return Convert.ToBase64String(encrypted);
    }

    private static string DecryptText(string cipherBase64, byte[] key)
    {
        byte[] bytes = Convert.FromBase64String(cipherBase64);
        byte[] decrypted = AES.DecryptFile(bytes, key);
        return Encoding.UTF8.GetString(decrypted);
    }
}