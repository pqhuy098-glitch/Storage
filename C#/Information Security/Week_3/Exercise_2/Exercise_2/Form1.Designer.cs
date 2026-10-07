namespace Exercise_2
{
    partial class Form1
    {
        /// <summary>
        ///  Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        ///  Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code

        /// <summary>
        ///  Required method for Designer support - do not modify
        ///  the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            InputBox = new RichTextBox();
            btnEncrypt = new Button();
            btnDecrypt = new Button();
            SuspendLayout();
            // 
            // InputBox
            // 
            InputBox.Location = new Point(158, 51);
            InputBox.Name = "InputBox";
            InputBox.Size = new Size(483, 166);
            InputBox.TabIndex = 0;
            InputBox.Text = "";
            // 
            // btnEncrypt
            // 
            btnEncrypt.Location = new Point(175, 249);
            btnEncrypt.Name = "btnEncrypt";
            btnEncrypt.Size = new Size(150, 46);
            btnEncrypt.TabIndex = 1;
            btnEncrypt.Text = "Encrypt";
            btnEncrypt.UseVisualStyleBackColor = true;
            // 
            // btnDecrypt
            // 
            btnDecrypt.Location = new Point(467, 249);
            btnDecrypt.Name = "btnDecrypt";
            btnDecrypt.Size = new Size(150, 46);
            btnDecrypt.TabIndex = 2;
            btnDecrypt.Text = "Decrypt";
            btnDecrypt.UseVisualStyleBackColor = true;
            // 
            // Form1
            // 
            AutoScaleDimensions = new SizeF(13F, 32F);
            AutoScaleMode = AutoScaleMode.Font;
            ClientSize = new Size(800, 450);
            Controls.Add(btnDecrypt);
            Controls.Add(btnEncrypt);
            Controls.Add(InputBox);
            Name = "Form1";
            Text = "Form1";
            ResumeLayout(false);
        }

        #endregion

        private RichTextBox InputBox;
        private Button btnEncrypt;
        private Button btnDecrypt;
    }
}
