using System.Numerics;

public class Program
{
    public static void Main(string[] args)
    {
        BigInteger p = 61;
        BigInteger q = 53;

        BigInteger n = p * q;
        BigInteger phi = (p - 1) * (q - 1);

        BigInteger e = 17;

        if (GCD(e, phi) != 1)
        {
            Console.WriteLine("e and phi are not coprime.");
        }
        BigInteger d = ModInverse(e, phi);
        Console.WriteLine("d: " + d);

        string message = "Hello World";
        string decryptedMessage = "";

        List<BigInteger> ciphertexts = new List<BigInteger>();

        foreach(char c in message)
        {
            BigInteger numberConverted = (BigInteger)c;
            BigInteger encryptedChar = BigInteger.ModPow(numberConverted, e, n);
            ciphertexts.Add(encryptedChar);
        }

        foreach (BigInteger c in ciphertexts)
        {
            BigInteger decrypted = BigInteger.ModPow(c, d, n);
            char decryptedChar = (char)(int)decrypted;
            decryptedMessage += decryptedChar;
        }

        Console.WriteLine("Ciphertext: ");
        foreach (BigInteger c in ciphertexts)
        {
            Console.Write(c + " ");
        }

        Console.WriteLine();
        Console.WriteLine("plaintext: " + decryptedMessage);
    }

    public static BigInteger GCD(BigInteger a, BigInteger b)
    {
        while (b != 0)
        {
            BigInteger temp = b;
            b = a % b;
            a = temp;
        }
        return a;
    }


    // x*e + Y*phy = 1 (x is d)
    // ax + by = c with c = 1
    public static BigInteger ModInverse(BigInteger e, BigInteger phi)
    {
        BigInteger t = 0;
        BigInteger newT = 1;    
        BigInteger r = phi;
        BigInteger newR = e;

        while (newR != 0)
        {
            BigInteger quotient = r / newR;

            BigInteger tempT = t;
            t = newT;
            newT = tempT - quotient * newT;

            BigInteger tempR = r;
            r = newR;
            newR = tempR - quotient * newR;
        }

        if (t < 0)
        {
            t += phi;
        }

        return t;
    }
}