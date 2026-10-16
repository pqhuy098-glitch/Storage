using System;
using System.Collections.Generic;
using System.Numerics;
using System.Text;
using System.Windows.Forms;

namespace Exercise_1
{
    public partial class Form1 : Form
    {
        private BigInteger p = 61;
        private BigInteger q = 53;
        private BigInteger n;
        private BigInteger phi;
        private BigInteger e = 17;
        private BigInteger d;

        public Form1()
        {
            InitializeComponent();
            InitializeRsaKeys();
            this.EncryptButton.Click += new System.EventHandler(this.EncryptButton_Click);
            this.DecryptButton.Click += new System.EventHandler(this.DecryptButton_Click);
        }

        private void InitializeRsaKeys()
        {
            n = p * q;
            phi = (p - 1) * (q - 1);

            if (GCD(e, phi) != 1)
            {
                MessageBox.Show("e and phi are not coprime!");
                return;
            }

            d = ModInverse(e, phi);
        }

        public void CloseWindows()
        {
            this.Close();
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

        private void EncryptButton_Click(object sender, EventArgs e)
        {
            string message = InputBox.Text;
            if (string.IsNullOrEmpty(message))
            {
                MessageBox.Show("Please enter text to encrypt!");
                return;
            }

            List<BigInteger> ciphertexts = new List<BigInteger>();

            foreach (char c in message)
            {
                BigInteger numberConverted = (BigInteger)c;
                BigInteger encryptedChar = BigInteger.ModPow(numberConverted, this.e, n);
                ciphertexts.Add(encryptedChar);
            }

            OutputBox.Text = string.Join(" ", ciphertexts);
        }

        private void DecryptButton_Click(object sender, EventArgs e)
        {
            string cipherInput = InputBox.Text;
            if (string.IsNullOrEmpty(cipherInput))
            {
                MessageBox.Show("Please enter text to decrypt!");
                return;
            }

            try
            {
                string[] parts = cipherInput.Split(new char[] { ' ' }, StringSplitOptions.RemoveEmptyEntries);
                string decryptedMessage = "";

                foreach (string part in parts)
                {
                    BigInteger c = BigInteger.Parse(part);
                    BigInteger decrypted = BigInteger.ModPow(c, d, n);
                    char decryptedChar = (char)(int)decrypted;
                    decryptedMessage += decryptedChar;
                }

                OutputBox.Text = decryptedMessage;
            }
            catch (Exception ex)
            {
                MessageBox.Show("Invalid cipher text format");
            }
        }
    }
}