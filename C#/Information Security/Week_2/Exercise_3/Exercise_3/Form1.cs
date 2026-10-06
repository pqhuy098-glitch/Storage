namespace Exercise_3
{
    public partial class Form1 : Form
    {
        public Form1()
        {
            InitializeComponent();

            this.Encrypt_Button.Click += new EventHandler(EncryptAndDecrypt);
            this.Decrypt_Button.Click += new EventHandler(EncryptAndDecrypt);
            this.Clear_Button.Click += new EventHandler(ClearContent);
            this.Close_Button.Click += new EventHandler(Close_Windows);
        }

        public void Close_Windows(object sender, EventArgs e)
        {
            this.Close();
        }

        public void ClearContent (object sender, EventArgs e)
        {
            Input_Text_Box.Clear();
            Output_Text_Box.Clear();
            KeyInput_Text_Box.Clear();
        }

        public void EncryptAndDecrypt(object sender, EventArgs e)
        {
            Button clickedButton = sender as Button;
            string content = Input_Text_Box.Text;
            string keyString = KeyInput_Text_Box.Text;
            string result = string.Empty;

            if (string.IsNullOrEmpty(keyString))
            {
                MessageBox.Show("Input key to encrypt or decrypt");
                return;
            }

            if (!int.TryParse(keyString, out int key))
            {
                MessageBox.Show("The key must be an integer");
                return;
            }

            if (key < 0)
            {
                MessageBox.Show("Key must be a possible number");
                return;
            }

            if (clickedButton.Name == "Encrypt_Button")
            {
                result = Encipher(content, key);
            }
            else if (clickedButton.Name == "Decrypt_Button")
            {
                result = Decipher(content, key);
            }

            Output_Text_Box.Text = result;

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
    }
}
