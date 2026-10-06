using System.Runtime.CompilerServices;

class Program
{
    private static bool checkFile(string filePath)
    {
        if (File.Exists(filePath) == true)
        {
            return true;
        }
        Console.WriteLine("File is not exist");
        return false;
    }

    private static char Cipher(char c, int key)
    {
        if (!char.IsLetter(c))
        {
            return c;
        }
        char offset = char.IsUpper(c) ? 'A' : 'a';
        return (char)((((c + key) - offset) % 26) + offset);
    }


    public static string Encipher(string input, int key)
    {
        string output = string.Empty;
        foreach (char c in input)
        {
            output += Cipher(c, key);
        }
        return output;
    }

    public static string Decipher(string input, int key)
    {
        return Encipher(input, 26 - key);
    }
    
    static void Main(string[] args)
    {
        Console.Write("Input file location: ");

        string path = Console.ReadLine();

        Console.Write("Input key: ");

        int key = int.Parse(Console.ReadLine());

        if (!checkFile(path))
        {
            return;
        }

        string text = File.ReadAllText(path);
        string cipherText = Encipher(text, key);
        string plainText = Decipher(text, key);

        string outputCipherText = "cipherText.txt";
        string outputPlainText = "plainText.txt";

        File.WriteAllText(outputCipherText, cipherText);
        File.WriteAllText(outputPlainText, plainText);

        Console.WriteLine(text);
        Console.WriteLine(cipherText);
        Console.WriteLine(plainText);
    }
}