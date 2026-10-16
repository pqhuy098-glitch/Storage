using System;
using System.IO;
using System.Security.Cryptography;
using System.Text;
using System.Windows.Forms;

namespace Exercise_2
{
    public partial class Form1 : Form
    {
        public Form1()
        {
            InitializeComponent();
            EncryptButton.Click += EncryptButton_Click;
        }

        private static readonly uint[] K = BuildK();

        private static uint[] BuildK()
        {
            uint[] k = new uint[64];
            for (int i = 0; i < 64; i++)
                k[i] = (uint)(long)(Math.Abs(Math.Sin(i + 1)) * 4294967296.0);
            return k;
        }

        private static readonly int[] S =
        {
            7, 12, 17, 22, 7, 12, 17, 22, 7, 12, 17, 22, 7, 12, 17, 22,
            5,  9, 14, 20, 5,  9, 14, 20, 5,  9, 14, 20, 5,  9, 14, 20,
            4, 11, 16, 23, 4, 11, 16, 23, 4, 11, 16, 23, 4, 11, 16, 23,
            6, 10, 15, 21, 6, 10, 15, 21, 6, 10, 15, 21, 6, 10, 15, 21
        };

        private static uint RotL(uint x, int c)
        {
            return (x << c) | (x >> (32 - c));
        }

        public static string ComputeMD5Manual(byte[] msg)
        {
            ulong bitLen = (ulong)msg.LongLength * 8UL;
            int rem = (int)(msg.LongLength % 64);
            int padLen = (rem < 56) ? (56 - rem) : (120 - rem);

            byte[] padded = new byte[msg.Length + padLen + 8];
            Array.Copy(msg, padded, msg.Length);
            padded[msg.Length] = 0x80;
            for (int i = 0; i < 8; i++)
                padded[padded.Length - 8 + i] = (byte)(bitLen >> (8 * i));

            uint a0 = 0x67452301;
            uint b0 = 0xefcdab89;
            uint c0 = 0x98badcfe;
            uint d0 = 0x10325476;

            uint[] M = new uint[16];

            for (int chunk = 0; chunk < padded.Length; chunk += 64)
            {
                for (int j = 0; j < 16; j++)
                {
                    int p = chunk + 4 * j;
                    M[j] = (uint)padded[p]
                         | ((uint)padded[p + 1] << 8)
                         | ((uint)padded[p + 2] << 16)
                         | ((uint)padded[p + 3] << 24);
                }

                uint A = a0, B = b0, C = c0, D = d0;

                for (int i = 0; i < 64; i++)
                {
                    uint F;
                    int g;

                    if (i < 16)
                    {
                        F = (B & C) | (~B & D);
                        g = i;
                    }
                    else if (i < 32)
                    {
                        F = (D & B) | (~D & C);
                        g = (5 * i + 1) % 16;
                    }
                    else if (i < 48)
                    {
                        F = B ^ C ^ D;
                        g = (3 * i + 5) % 16;
                    }
                    else
                    {
                        F = C ^ (B | ~D);
                        g = (7 * i) % 16;
                    }

                    F = F + A + K[i] + M[g];
                    A = D;
                    D = C;
                    C = B;
                    B = B + RotL(F, S[i]);
                }

                a0 += A;
                b0 += B;
                c0 += C;
                d0 += D;
            }

            uint[] regs = { a0, b0, c0, d0 };
            StringBuilder sb = new StringBuilder();
            foreach (uint r in regs)
                for (int k = 0; k < 4; k++)
                    sb.Append(((r >> (8 * k)) & 0xFF).ToString("x2"));

            return sb.ToString();
        }

        public static string ComputeMD5Manual(string input)
        {
            return ComputeMD5Manual(Encoding.UTF8.GetBytes(input));
        }

        public static string ComputeMD5File(string path)
        {
            return ComputeMD5Manual(File.ReadAllBytes(path));
        }

        private void EncryptButton_Click(object sender, EventArgs e)
        {
            string plainText = InputBox.Text;
            if (string.IsNullOrEmpty(plainText))
            {
                MessageBox.Show("Enter the text to hash");
                return;
            }

            OutputBox.Text = ComputeMD5Manual(plainText);
        }
    }
}