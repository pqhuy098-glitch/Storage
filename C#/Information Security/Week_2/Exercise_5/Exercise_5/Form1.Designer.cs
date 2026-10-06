namespace Exercise_5
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
            KeyInput_Box = new RichTextBox();
            label1 = new Label();
            label2 = new Label();
            label3 = new Label();
            Encrypt_Button = new Button();
            Decrypt_Button = new Button();
            Save_Button = new Button();
            Clear_Button = new Button();
            Exit_Button = new Button();
            SuspendLayout();
            // 
            // Input_Box
            // 
            Input_Box.Location = new Point(16, 39);
            Input_Box.Name = "Input_Box";
            Input_Box.Size = new Size(479, 148);
            Input_Box.TabIndex = 0;
            Input_Box.Text = "";
            // 
            // Output_Box
            // 
            Output_Box.Location = new Point(16, 240);
            Output_Box.Name = "Output_Box";
            Output_Box.Size = new Size(479, 148);
            Output_Box.TabIndex = 1;
            Output_Box.Text = "";
            // 
            // KeyInput_Box
            // 
            KeyInput_Box.Location = new Point(528, 39);
            KeyInput_Box.Name = "KeyInput_Box";
            KeyInput_Box.Size = new Size(250, 148);
            KeyInput_Box.TabIndex = 2;
            KeyInput_Box.Text = "";
            // 
            // label1
            // 
            label1.AutoSize = true;
            label1.Location = new Point(16, 9);
            label1.Name = "label1";
            label1.Size = new Size(43, 20);
            label1.TabIndex = 3;
            label1.Text = "Input";
            // 
            // label2
            // 
            label2.AutoSize = true;
            label2.Location = new Point(16, 207);
            label2.Name = "label2";
            label2.Size = new Size(49, 20);
            label2.TabIndex = 4;
            label2.Text = "Result";
            // 
            // label3
            // 
            label3.AutoSize = true;
            label3.Location = new Point(528, 9);
            label3.Name = "label3";
            label3.Size = new Size(33, 20);
            label3.TabIndex = 5;
            label3.Text = "Key";
            // 
            // Encrypt_Button
            // 
            Encrypt_Button.Location = new Point(544, 240);
            Encrypt_Button.Name = "Encrypt_Button";
            Encrypt_Button.Size = new Size(94, 29);
            Encrypt_Button.TabIndex = 6;
            Encrypt_Button.Text = "Encrypt";
            Encrypt_Button.UseVisualStyleBackColor = true;
            // 
            // Decrypt_Button
            // 
            Decrypt_Button.Location = new Point(659, 240);
            Decrypt_Button.Name = "Decrypt_Button";
            Decrypt_Button.Size = new Size(94, 29);
            Decrypt_Button.TabIndex = 8;
            Decrypt_Button.Text = "Decrypt";
            Decrypt_Button.UseVisualStyleBackColor = true;
            // 
            // Save_Button
            // 
            Save_Button.Location = new Point(544, 289);
            Save_Button.Name = "Save_Button";
            Save_Button.Size = new Size(94, 29);
            Save_Button.TabIndex = 9;
            Save_Button.Text = "Save";
            Save_Button.UseVisualStyleBackColor = true;
            // 
            // Clear_Button
            // 
            Clear_Button.Location = new Point(659, 289);
            Clear_Button.Name = "Clear_Button";
            Clear_Button.Size = new Size(94, 29);
            Clear_Button.TabIndex = 10;
            Clear_Button.Text = "Clear";
            Clear_Button.UseVisualStyleBackColor = true;
            // 
            // Exit_Button
            // 
            Exit_Button.Location = new Point(544, 334);
            Exit_Button.Name = "Exit_Button";
            Exit_Button.Size = new Size(209, 29);
            Exit_Button.TabIndex = 11;
            Exit_Button.Text = "Exit";
            Exit_Button.UseVisualStyleBackColor = true;
            // 
            // Form1
            // 
            AutoScaleDimensions = new SizeF(8F, 20F);
            AutoScaleMode = AutoScaleMode.Font;
            ClientSize = new Size(800, 450);
            Controls.Add(Exit_Button);
            Controls.Add(Clear_Button);
            Controls.Add(Save_Button);
            Controls.Add(Decrypt_Button);
            Controls.Add(Encrypt_Button);
            Controls.Add(label3);
            Controls.Add(label2);
            Controls.Add(label1);
            Controls.Add(KeyInput_Box);
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
        private RichTextBox KeyInput_Box;
        private Label label1;
        private Label label2;
        private Label label3;
        private Button Encrypt_Button;
        private Button Decrypt_Button;
        private Button Save_Button;
        private Button Clear_Button;
        private Button Exit_Button;
    }
}
