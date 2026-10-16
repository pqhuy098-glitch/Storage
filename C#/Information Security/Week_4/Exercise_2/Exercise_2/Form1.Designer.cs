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
            OutputBox = new RichTextBox();
            EncryptButton = new Button();
            SuspendLayout();
            // 
            // InputBox
            // 
            InputBox.Location = new Point(91, 46);
            InputBox.Name = "InputBox";
            InputBox.Size = new Size(610, 121);
            InputBox.TabIndex = 0;
            InputBox.Text = "";
            // 
            // OutputBox
            // 
            OutputBox.Location = new Point(91, 272);
            OutputBox.Name = "OutputBox";
            OutputBox.Size = new Size(613, 141);
            OutputBox.TabIndex = 1;
            OutputBox.Text = "";
            // 
            // EncryptButton
            // 
            EncryptButton.Location = new Point(314, 189);
            EncryptButton.Name = "EncryptButton";
            EncryptButton.Size = new Size(180, 56);
            EncryptButton.TabIndex = 2;
            EncryptButton.Text = "Encrypt";
            EncryptButton.UseVisualStyleBackColor = true;
            // 
            // Form1
            // 
            AutoScaleDimensions = new SizeF(13F, 32F);
            AutoScaleMode = AutoScaleMode.Font;
            ClientSize = new Size(800, 450);
            Controls.Add(EncryptButton);
            Controls.Add(OutputBox);
            Controls.Add(InputBox);
            Name = "Form1";
            Text = "Form1";
            ResumeLayout(false);
        }

        #endregion

        private RichTextBox InputBox;
        private RichTextBox OutputBox;
        private Button EncryptButton;
    }
}
