 class Program
{
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
        string text = "IRXUVFRUHDQGVHYHQBHDUVDJR";
        int key = 3;
        string plainText = Decipher(text, key);
        Console.WriteLine("Ciphertext: " + text);
        Console.WriteLine("Plaintext: " + plainText);
        Console.ReadLine();
    }
}