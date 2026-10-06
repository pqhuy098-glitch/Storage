using System;
using System.Text;

class Program
{
    static void Main(string[] args)
    {
        Console.Write("Input put the path location: ");
        string path = Console.ReadLine();

        if (!File.Exists(path))
        {
            Console.WriteLine("This file doesn't exist");
            return;
        }

        string plainText = File.ReadAllText(path);

        Console.Write("Input the key (like: 1 2 3 4 5): ");
        string keyInput = Console.ReadLine();

        int[] key;
        if (keyInput.Contains(" "))
        {
            key = keyInput.Split(new[] { ' ' }, StringSplitOptions.RemoveEmptyEntries)
                          .Select(int.Parse)
                          .ToArray();
        }
        else
        {
            key = keyInput.Select(c => c - '0').ToArray();
        }

        string cipherText = PermEncrypt(plainText, key);

        Console.WriteLine(plainText);
        Console.WriteLine(cipherText);
        Console.ReadLine();

        string outputPath = "encrypted.txt";
        File.WriteAllText(outputPath, cipherText);
    }

    static string PermEncrypt(string text, int[] key)
    {
        int m = key.Length;

        while (text.Length % m != 0)
        {
            text += "x";
        }

        StringBuilder sb = new StringBuilder();

        for (int i = 0; i < text.Length; i += m)
        {
            for (int j = 0; j < m; j++)
            {
                sb.Append(text[i + key[j] - 1]);
            }
        }

        return sb.ToString();
    }
}