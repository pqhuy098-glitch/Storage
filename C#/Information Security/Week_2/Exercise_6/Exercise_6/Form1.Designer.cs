namespace Exercise_6
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
            Input_Box = new RichTextBox();
            Output_Box = new RichTextBox();
            Key_1_Box = new RichTextBox();
            Key_2_Box = new RichTextBox();
            Decrypt_Button = new Button();
            Encrypt_Button = new Button();
            Save_Button = new Button();
            Clear_Button = new Button();
            Exit_Button = new Button();
            label1 = new Label();
            label2 = new Label();
            label3 = new Label();
            label4 = new Label();
            SuspendLayout();
            // 
            // Input_Box
            // 
            Input_Box.Location = new Point(10, 35);
            Input_Box.Name = "Input_Box";
            Input_Box.Size = new Size(518, 164);
            Input_Box.TabIndex = 0;
            Input_Box.Text = "";
            // 
            // Output_Box
            // 
            Output_Box.Location = new Point(10, 264);
            Output_Box.Name = "Output_Box";
            Output_Box.Size = new Size(518, 164);
            Output_Box.TabIndex = 1;
            Output_Box.Text = "";
            // 
            // Key_1_Box
            // 
            Key_1_Box.Location = new Point(606, 35);
            Key_1_Box.Name = "Key_1_Box";
            Key_1_Box.Size = new Size(182, 74);
            Key_1_Box.TabIndex = 2;
            Key_1_Box.Text = "";
            // 
            // Key_2_Box
            // 
            Key_2_Box.Location = new Point(606, 125);
            Key_2_Box.Name = "Key_2_Box";
            Key_2_Box.Size = new Size(182, 74);
            Key_2_Box.TabIndex = 3;
            Key_2_Box.Text = "";
            // 
            // Decrypt_Button
            // 
            Decrypt_Button.Location = new Point(696, 264);
            Decrypt_Button.Name = "Decrypt_Button";
            Decrypt_Button.Size = new Size(92, 36);
            Decrypt_Button.TabIndex = 5;
            Decrypt_Button.Text = "Decrypt";
            Decrypt_Button.UseVisualStyleBackColor = true;
            // 
            // Encrypt_Button
            // 
            Encrypt_Button.Location = new Point(598, 264);
            Encrypt_Button.Name = "Encrypt_Button";
            Encrypt_Button.Size = new Size(92, 36);
            Encrypt_Button.TabIndex = 4;
            Encrypt_Button.Text = "Encrypt";
            Encrypt_Button.UseVisualStyleBackColor = true;
            // 
            // Save_Button
            // 
            Save_Button.Location = new Point(598, 317);
            Save_Button.Name = "Save_Button";
            Save_Button.Size = new Size(92, 36);
            Save_Button.TabIndex = 6;
            Save_Button.Text = "Save";
            Save_Button.UseVisualStyleBackColor = true;
            // 
            // Clear_Button
            // 
            Clear_Button.Location = new Point(696, 317);
            Clear_Button.Name = "Clear_Button";
            Clear_Button.Size = new Size(92, 36);
            Clear_Button.TabIndex = 7;
            Clear_Button.Text = "Clear";
            Clear_Button.UseVisualStyleBackColor = true;
            // 
            // Exit_Button
            // 
            Exit_Button.Location = new Point(598, 371);
            Exit_Button.Name = "Exit_Button";
            Exit_Button.Size = new Size(190, 36);
            Exit_Button.TabIndex = 8;
            Exit_Button.Text = "Exit";
            Exit_Button.UseVisualStyleBackColor = true;
            // 
            // label1
            // 
            label1.AutoSize = true;
            label1.Location = new Point(12, 12);
            label1.Name = "label1";
            label1.Size = new Size(43, 20);
            label1.TabIndex = 9;
            label1.Text = "Input";
            // 
            // label2
            // 
            label2.AutoSize = true;
            label2.Location = new Point(12, 241);
            label2.Name = "label2";
            label2.Size = new Size(55, 20);
            label2.TabIndex = 10;
            label2.Text = "Output";
            // 
            // label3
            // 
            label3.AutoSize = true;
            label3.Location = new Point(550, 62);
            label3.Name = "label3";
            label3.Size = new Size(45, 20);
            label3.TabIndex = 11;
            label3.Text = "Key 1";
            // 
            // label4
            // 
            label4.AutoSize = true;
            label4.Location = new Point(550, 154);
            label4.Name = "label4";
            label4.Size = new Size(45, 20);
            label4.TabIndex = 12;
            label4.Text = "Key 2";
            // 
            // Form1
            // 
            AutoScaleDimensions = new SizeF(8F, 20F);
            AutoScaleMode = AutoScaleMode.Font;
            ClientSize = new Size(800, 450);
            Controls.Add(label4);
            Controls.Add(label3);
            Controls.Add(label2);
            Controls.Add(label1);
            Controls.Add(Exit_Button);
            Controls.Add(Clear_Button);
            Controls.Add(Save_Button);
            Controls.Add(Decrypt_Button);
            Controls.Add(Encrypt_Button);
            Controls.Add(Key_2_Box);
            Controls.Add(Key_1_Box);
            Controls.Add(Output_Box);
            Controls.Add(Input_Box);
            Name = "Form1";
            Text = "Form1";
            ResumeLayout(false);
            PerformLayout();
        }

        #endregion

        private RichTextBox Input_Box;
        private RichTextBox Output_Box;
        private RichTextBox Key_1_Box;
        private RichTextBox Key_2_Box;
        private Button Decrypt_Button;
        private Button Encrypt_Button;
        private Button Save_Button;
        private Button Clear_Button;
        private Button Exit_Button;
        private Label label1;
        private Label label2;
        private Label label3;
        private Label label4;
    }
}
