namespace Exercise_6
{
    public partial class Form1 : Form
    {
        public Form1()
        {
            InitializeComponent();

            this.Encrypt_Button.Click += new EventHandler(EncryptAndDecrypt);
            this.Decrypt_Button.Click += new EventHandler(EncryptAndDecrypt);
            this.Clear_Button.Click += new EventHandler(ClearContent);
            this.Exit_Button.Click += new EventHandler(Close_Windows);
        }

        public void Close_Windows(object sender, EventArgs e)
        {
            this.Close();
        }

        public void ClearContent(object sender, EventArgs e)
        {
            Input_Box.Clear();
            Output_Box.Clear();
            Key_1_Box.Clear();
            Key_2_Box.Clear();
        }

        private static int GCD(int a, int b)
        {
            while (b != 0)
            {
                int t = b;
                b = a % b;
                a = t;
            }
            return a;
        }


        public void EncryptAndDecrypt(object sender, EventArgs e)
        {
            Button clickedButton = sender as Button;

            string content = Input_Box.Text;
            string key_1 = Key_1_Box.Text;
            string key_2 = Key_2_Box.Text;
            string result = string.Empty;

            if (string.IsNullOrEmpty(key_1) || string.IsNullOrEmpty(key_2))
            {
                MessageBox.Show("Input key to encrypt or decrypt");
                return;
            }

            if (!int.TryParse(key_1, out int key1) || !int.TryParse(key_2, out int key2))
            {
                MessageBox.Show("The key must be an integer");
                return;
            }

            if (key1 < 0 && key2 < 0)
            {
                MessageBox.Show("Key must be a possible number");
                return;
            }

            if (GCD(key1, 26) != 1)
            {
                MessageBox.Show("Key 1 must be coprime with 26 (1, 3, 5, 7, 9, 11, 15, 17, 19, 21, 23, 25)");
                return;
            }


            if (clickedButton.Name == "Encrypt_Button")
            {
                result = AffineEncrypt(content, key1, key2);
            }
            else if (clickedButton.Name == "Decrypt_Button")
            {
                result = AffineDecrypt(content, key1, key2);
            }

            Output_Box.Text = result;

        }

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
    }
}
