namespace Exercise_5
{
    public partial class Form1 : Form
    {
        public Form1()
        {
            InitializeComponent();

            this.Exit_Button.Click += new EventHandler(Close_Windows);
            this.Clear_Button.Click += new EventHandler(ClearContent);
            this.Encrypt_Button.Click += new EventHandler(EncryptAndDecrypt);
            this.Decrypt_Button.Click += new EventHandler(EncryptAndDecrypt);
        }

        public void Close_Windows(object sender, EventArgs e)
        {
            this.Close();
        }

        public void ClearContent(object sender, EventArgs e) 
        { 
            this.Input_Box.Clear();
            this.Output_Box.Clear();
            this.KeyInput_Box.Clear();
        }

        public void EncryptAndDecrypt(object sender, EventArgs e)
        {
            string content = Input_Box.Text;
            string result = string.Empty;
            string cipherAlphabet = KeyInput_Box.Text.ToLower();

            if (string.IsNullOrEmpty(cipherAlphabet))
            {
                MessageBox.Show("Input key to encrypt or decrypt");
                return;
            }

            if (cipherAlphabet.Length != 26 || cipherAlphabet.Distinct().Count() != 26)
            {
                MessageBox.Show("Key must contain exactly 26 unique letters");
                return;
            }

            if (sender == Encrypt_Button)
                Encipher(content, cipherAlphabet, out result);
            else if (sender == Decrypt_Button)
                Decipher(content, cipherAlphabet, out result);

            Output_Box.ReadOnly = true;
            Output_Box.Text = result;
        }


        private static bool Cipher(string input, string oldAlphabet, string newAlphabet, out string output)
        {
            output = string.Empty;

            if (oldAlphabet.Length != newAlphabet.Length)
            {
                return false;
            }

            for (int i = 0; i < input.Length; i++)
            {
                int oldCharIndex = oldAlphabet.IndexOf(char.ToLower(input[i]));

                if (oldCharIndex >= 0)
                {
                    output += char.IsUpper(input[i]) ? char.ToUpper(newAlphabet[oldCharIndex]) : newAlphabet[oldCharIndex];
                }
                else
                {
                    output += input[i];
                }
            }
            return true;
        }

        public static bool Encipher(string input, string cipherAlphabet, out string output)
        {
            string plainAlphabet = "abcdefghijklmnopqrstuvwxyz";
            return Cipher(input, plainAlphabet, cipherAlphabet, out output);
        }

        public static bool Decipher(string input, string cipherAlphabet, out string output)
        {
            string plainAlphabet = "abcdefghijklmnopqrstuvwxyz";
            return Cipher(input, cipherAlphabet, plainAlphabet, out output);
        }
    }
}
