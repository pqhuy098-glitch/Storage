class Program
{
    private const uint DECRYPT = 1;
    private const uint ENCRYPT = 0;

    private static byte[] IP_Table =
    {
        58, 50, 42, 34, 26, 18, 10, 2,
        60, 52, 44, 36, 28, 20, 12, 4,
        62, 54, 46, 38, 30, 22, 14, 6,
        64, 56, 48, 40, 32, 24, 16, 8,
        57, 49, 41, 33, 25, 17, 9,  1,
        59, 51, 43, 35, 27, 19, 11, 3,
        61, 53, 45, 37, 29, 21, 13, 5,
        63, 55, 47, 39, 31, 23, 15, 7
    };

    private static byte[] InvIP_Table =
    {
        40, 8, 48, 16, 56, 24, 64, 32,
        39, 7, 47, 15, 55, 23, 63, 31,
        38, 6, 46, 14, 54, 22, 62, 30,
        37, 5, 45, 13, 53, 21, 61, 29,
        36, 4, 44, 12, 52, 20, 60, 28,
        35, 3, 43, 11, 51, 19, 59, 27,
        34, 2, 42, 10, 50, 18, 58, 26,
        33, 1, 41,  9, 49, 17, 57, 25
    };

    private static byte[] E_Table =
    {
        32,  1,  2,  3,  4,  5,
         4,  5,  6,  7,  8,  9,
         8,  9, 10, 11, 12, 13,
        12, 13, 14, 15, 16, 17,
        16, 17, 18, 19, 20, 21,
        20, 21, 22, 23, 24, 25,
        24, 25, 26, 27, 28, 29,
        28, 29, 30, 31, 32,  1
    };

    private static readonly byte[] P_Table =
    {
        16,  7, 20, 21,
        29, 12, 28, 17,
         1, 15, 23, 26,
         5, 18, 31, 10,
         2,  8, 24, 14,
        32, 27,  3,  9,
        19, 13, 30,  6,
        22, 11,  4, 25
    };

    private static uint BITNUM(byte[] array, int bitPos, int targetShift)
    {
        int byteIndex = bitPos / 8;
        int bitOffset = 7 - (bitPos % 8);
        uint bit = (uint)((array[byteIndex] >> bitOffset) & 0x01);
        return bit << targetShift;
    }

    private static uint BITNUMINTR(uint value, int bitPos, int targetShift)
    {
        int bitOffset = 31 - bitPos;
        uint bit = (value >> bitOffset) & 0x01;
        return bit << targetShift;
    }

    private static uint SBOXBIT(byte a)
    {
        return (uint)(((a) & 0x20) | (((a) & 0x1f) >> 1) | (((a) & 0x01) << 4));
    }

    private static byte[] sbox1 =
    {
        14, 4, 13, 1, 2, 15, 11, 8, 3, 10, 6, 12, 5, 9, 0, 7, 0, 15, 7, 4, 14,
        2, 13, 1, 10, 6, 12, 11, 9, 5, 3, 8, 4, 1, 14, 8, 13, 6, 2, 11, 15, 12,
        9, 7, 3, 10, 5, 0, 15, 12, 8, 2, 4, 9, 1, 7, 5, 11, 3, 14, 10, 0, 6, 13
    };

    private static byte[] sbox2 =
    {
         15, 1, 8, 14, 6, 11, 3, 4, 9, 7, 2, 13, 12, 0, 5, 10, 3, 13, 4, 7, 15,
         2, 8, 14, 12, 0, 1, 10, 6, 9, 11, 5, 0, 14, 7, 11, 10, 4, 13, 1, 5, 8,
         12, 6, 9, 3, 2, 15, 13, 8, 10, 1, 3, 15, 4, 2, 11, 6, 7, 12, 0, 5, 14, 9
    };

    private static byte[] sbox3 =
    {
         10, 0, 9, 14, 6, 3, 15, 5, 1, 13, 12, 7, 11, 4, 2, 8, 13, 7, 0, 9, 3, 4,
         6, 10, 2, 8, 5, 14, 12, 11, 15, 1, 13, 6, 4, 9, 8, 15, 3, 0, 11, 1, 2, 12,
         5, 10, 14, 7, 1, 10, 13, 0, 6, 9, 8, 7, 4, 15, 14, 3, 11, 5, 2, 12
    };

    private static byte[] sbox4 =
    {
         7, 13, 14, 3, 0, 6, 9, 10, 1, 2, 8, 5, 11, 12, 4, 15, 13, 8, 11, 5, 6, 15,
         0, 3, 4, 7, 2, 12, 1, 10, 14, 9, 10, 6, 9, 0, 12, 11, 7, 13, 15, 1, 3, 14,
         5, 2, 8, 4, 3, 15, 0, 6, 10, 1, 13, 8, 9, 4, 5, 11, 12, 7, 2, 14
     };

    private static byte[] sbox5 =
    {
         2, 12, 4, 1, 7, 10, 11, 6, 8, 5, 3, 15, 13, 0, 14, 9, 14, 11, 2, 12, 4,
         7, 13, 1, 5, 0, 15, 10, 3, 9, 8, 6, 4, 2, 1, 11, 10, 13, 7, 8, 15, 9, 12,
         5, 6, 3, 0, 14, 11, 8, 12, 7, 1, 14, 2, 13, 6, 15, 0, 9, 10, 4, 5, 3
     };

    private static byte[] sbox6 =
    {
        12, 1, 10, 15, 9, 2, 6, 8, 0, 13, 3, 4, 14, 7, 5, 11, 10, 15, 4, 2, 7, 12,
        9, 5, 6, 1, 13, 14, 0, 11, 3, 8, 9, 14, 15, 5, 2, 8, 12, 3, 7, 0, 4, 10,
        1, 13, 11, 6, 4, 3, 2, 12, 9, 5, 15, 10, 11, 14, 1, 7, 6, 0, 8, 13
    };

    private static byte[] sbox7 =
    {
         4, 11, 2, 14, 15, 0, 8, 13, 3, 12, 9, 7, 5, 10, 6, 1, 13, 0, 11, 7, 4, 9,
         1, 10, 14, 3, 5, 12, 2, 15, 8, 6, 1, 4, 11, 13, 12, 3, 7, 14, 10, 15, 6,
         8, 0, 5, 9, 2, 6, 11, 13, 8, 1, 4, 10, 7, 9, 5, 0, 15, 14, 2, 3, 12
    };

    private static byte[] sbox8 =
    {
         13, 2, 8, 4, 6, 15, 11, 1, 10, 9, 3, 14, 5, 0, 12, 7, 1, 15, 13, 8, 10,
         3, 7, 4, 12, 5, 6, 11, 0, 14, 9, 2, 7, 11, 4, 1, 9, 12, 14, 2, 0, 6, 10,
         13, 15, 3, 5, 8, 2, 1, 14, 7, 4, 10, 8, 13, 15, 12, 9, 0, 3, 5, 6, 11
    };

    private static byte[][] S_Boxes =
    {
        sbox1, sbox2, sbox3, sbox4, sbox5, sbox6, sbox7, sbox8
    };

    public static void KeySchedule(byte[] key, byte[][] schedule, uint mode)
    {
        uint i;
        uint j;
        uint toGen;
        uint C;
        uint D;

        uint[] key_rnd_shift =
        {
            1, 1, 2, 2, 2, 2, 2, 2, 1, 2, 2, 2, 2, 2, 2, 1
        };
        uint[] key_perm_c =
        {
            56, 48, 40, 32, 24, 16, 8, 0, 57, 49, 41, 33, 25, 17, 9, 1, 58,
            50, 42, 34, 26, 18, 10, 2, 59, 51, 43, 35
        };
        uint[] key_perm_d =
        {
            62, 54, 46, 38, 30, 22, 14, 6, 61, 53, 45, 37, 29, 21, 13, 5, 60,
            52, 44, 36, 28, 20, 12, 4, 27, 19, 11, 3
        };
        uint[] key_compression =
        {
            13, 16, 10, 23, 0, 4, 2, 27, 14, 5, 20, 9, 22, 18, 11, 3, 25, 7, 15,
            6, 26, 19, 12, 1, 40, 51, 30, 36, 46, 54, 29, 39, 50, 44, 32, 47, 43,
            48, 38, 55, 33, 52, 45, 41, 49, 35, 28, 31
        };

        for (i = 0, j = 31, C = 0; i < 28; ++i, --j)
        {
            C |= BITNUM(key, (int)key_perm_c[i], (int)j);
        }

        for (i = 0, j = 31, D = 0; i < 28; ++i, --j)
        {
            D |= BITNUM(key, (int)key_perm_d[i], (int)j);
        }

        for (i = 0; i < 16; i++)
        {
            C = ((C << (int)key_rnd_shift[i]) | (C >> (28 - (int)key_rnd_shift[i]))) & 0xfffffff0;
            D = ((D << (int)key_rnd_shift[i]) | (D >> (28 - (int)key_rnd_shift[i]))) & 0xfffffff0;

            if (mode == DECRYPT)
            {
                toGen = 15 - i;
            }
            else
            {
                toGen = i;
            }

            for (j = 0; j < 6; j++)
            {
                schedule[toGen][j] = 0;
            }

            for (j = 0; j < 24; j++)
            {
                schedule[toGen][j / 8] |= (byte)BITNUMINTR(C, (int)key_compression[j], (int)(7 - (j % 8)));
            }

            for (; j < 48; j++)
            {
                schedule[toGen][j / 8] |= (byte)BITNUMINTR(D, (int)key_compression[j] - 28, (int)(7 - (j % 8)));
            }
        }
    }

    private static void IP(uint[] state, byte[] input)
    {
        state[0] = 0;
        state[1] = 0;

        for (int i = 0; i < 32; i++)
        {
            state[0] |= BITNUM(input, IP_Table[i] - 1, 31 - i);
            state[1] |= BITNUM(input, IP_Table[i + 32] - 1, 31 - i);
        }
    }

    private static void InvIP(uint[] state, byte[] output)
    {
        Array.Clear(output, 0, output.Length);

        for (int i = 0; i < 64; i++)
        {
            int srcBitPos = InvIP_Table[i] - 1;
            uint bit;

            if (srcBitPos < 32)
                bit = (state[0] >> (31 - srcBitPos)) & 0x01;
            else
                bit = (state[1] >> (31 - (srcBitPos - 32))) & 0x01;

            int byteIndex = i / 8;
            int bitOffset = 7 - (i % 8);
            output[byteIndex] |= (byte)(bit << bitOffset);
        }
    }

    private static uint F(uint R, byte[] subkey)
    {
        byte[] expandedR = new byte[6];
        for (int j = 0; j < 48; j++)
        {
            uint bit = BITNUMINTR(R, E_Table[j] - 1, 7 - (j % 8));
            expandedR[j / 8] |= (byte)bit;
        }

        for (int j = 0; j < 6; j++)
        {
            expandedR[j] ^= subkey[j];
        }

        uint sboxOutput = 0;
        for (int i = 0; i < 8; i++)
        {
            int bitPos = i * 6;
            byte b1 = (byte)((expandedR[bitPos / 8] >> (7 - (bitPos % 8))) & 0x01);
            byte b2 = (byte)((expandedR[(bitPos + 1) / 8] >> (7 - ((bitPos + 1) % 8))) & 0x01);
            byte b3 = (byte)((expandedR[(bitPos + 2) / 8] >> (7 - ((bitPos + 2) % 8))) & 0x01);
            byte b4 = (byte)((expandedR[(bitPos + 3) / 8] >> (7 - ((bitPos + 3) % 8))) & 0x01);
            byte b5 = (byte)((expandedR[(bitPos + 4) / 8] >> (7 - ((bitPos + 4) % 8))) & 0x01);
            byte b6 = (byte)((expandedR[(bitPos + 5) / 8] >> (7 - ((bitPos + 5) % 8))) & 0x01);

            int row = (b1 << 1) | b6;
            int col = (b2 << 3) | (b3 << 2) | (b4 << 1) | b5;

            byte val = S_Boxes[i][row * 16 + col];
            sboxOutput |= ((uint)val << (28 - (i * 4)));
        }

        uint pOutput = 0;
        for (int i = 0; i < 32; i++)
        {
            pOutput |= BITNUMINTR(sboxOutput, P_Table[i] - 1, 31 - i);
        }

        return pOutput;
    }

    public static void Crypt(byte[] input, byte[] output, byte[][] key)
    {
        uint[] state = new uint[2];
        uint idx;
        uint t;

        IP(state, input);

        for (idx = 0; idx < 15; ++idx)
        {
            t = state[1];
            state[1] = F(state[1], key[idx]) ^ state[0];
            state[0] = t;
        }

        InvIP(state, output);
    }

    private static string PrintBytes(byte[] bytes)
    {
        return BitConverter.ToString(bytes).Replace("-", " ").ToLower();
    }

    public static void Main(string[] args)
    {
        byte[] key = { 0x13, 0x34, 0x57, 0x79, 0x9b, 0xbc, 0xdf, 0xf1 };
        byte[] plaintext = { 0x01, 0x23, 0x45, 0x67, 0x89, 0xab, 0xcd, 0xe7 };

        byte[][] encryptSchedule = new byte[16][];
        byte[][] decryptSchedule = new byte[16][];
        for (int i = 0; i < 16; i++)
        {
            encryptSchedule[i] = new byte[6];
            decryptSchedule[i] = new byte[6];
        }

        byte[] cipherText = new byte[8];
        byte[] decryptedText = new byte[8];

        KeySchedule(key, encryptSchedule, ENCRYPT);

        Crypt(plaintext, cipherText, encryptSchedule);
        Console.WriteLine("Encrypt Output: " + PrintBytes(cipherText));

        KeySchedule(key, decryptSchedule, DECRYPT);

        Crypt(cipherText, decryptedText, decryptSchedule);
        Console.WriteLine("Decrypt Output: " + PrintBytes(decryptedText));
    }
}