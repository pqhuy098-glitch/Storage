namespace Affine_Cons
{
    class Program
    {
        public static string AffineEncrypt(string plainText, int a, int b)
        {
            string cipherText = "";
            char[] chars = plainText.ToUpper().ToCharArray();

            foreach (char c in chars)
            {
                int x = Convert.ToInt32(c - 65);
                cipherText += Convert.ToChar(((a * x + b) % 26) + 65);

            }

            return cipherText;
        }

        public static string AffineDecrypt(string cipherText, int a, int b)
        {
            string plainText = "";
            int aInverse = MultiplicativeInverse(a);
            char[] chars = cipherText.ToUpper().ToCharArray();

            foreach (char c in chars)
            {
                int x = Convert.ToInt32(c - 65);
                if (x - b < 0)
                {
                    x = Convert.ToInt32(x) + 26;
                }
                plainText += Convert.ToChar(((aInverse * (x - b)) % 26) + 65);
            }

            return plainText;
        }

        public static int MultiplicativeInverse(int a)
        {
            for (int x = 1; x < 27; x++)
            {
                if ((a * x) % 26 == 1)
                {
                    return x;
                }

            }

            throw new Exception("No Multiplicative inverse found!");

        }

        static void Main(string[] args)
        {
            string text = "The quick brown for jumps over the lazy dog";
            string cipherText = AffineEncrypt(text, 3, 2);
            string plainText = AffineDecrypt(cipherText, 3, 2);
            Console.WriteLine(plainText);
            Console.WriteLine(cipherText);
            Console.ReadLine();
        }   
    }
}