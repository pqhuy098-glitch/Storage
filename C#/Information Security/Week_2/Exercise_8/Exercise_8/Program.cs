using System;
using System.Text;

class Program
{
    static void Main(string[] args)
    {
        string plainText = "we are all together";
        int[] key = { 2, 4, 1, 5, 3 };

        string cipherText = PermEncrypt(plainText, key);

        Console.WriteLine(plainText);
        Console.WriteLine(cipherText);
        Console.ReadLine();
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