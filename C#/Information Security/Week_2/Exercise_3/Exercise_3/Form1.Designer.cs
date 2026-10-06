namespace Exercise_3
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
            Input_Text_Box = new RichTextBox();
            Input_Label = new Label();
            Output_Text_Box = new RichTextBox();
            Output_Label = new Label();
            Encrypt_Button = new Button();
            KeyInput_Text_Box = new RichTextBox();
            Decrypt_Button = new Button();
            Clear_Button = new Button();
            Save_Button = new Button();
            Close_Button = new Button();
            Cryptanalysis_Button = new Button();
            Open_Button = new Button();
            SuspendLayout();
            // 
            // Input_Text_Box
            // 
            Input_Text_Box.Location = new Point(19, 46);
            Input_Text_Box.Name = "Input_Text_Box";
            Input_Text_Box.Size = new Size(763, 135);
            Input_Text_Box.TabIndex = 0;
            Input_Text_Box.Text = "";
            // 
            // Input_Label
            // 
            Input_Label.AutoSize = true;
            Input_Label.Location = new Point(19, 23);
            Input_Label.Name = "Input_Label";
            Input_Label.Size = new Size(43, 20);
            Input_Label.TabIndex = 1;
            Input_Label.Text = "Input";
            // 
            // Output_Text_Box
            // 
            Output_Text_Box.Location = new Point(19, 282);
            Output_Text_Box.Name = "Output_Text_Box";
            Output_Text_Box.Size = new Size(763, 133);
            Output_Text_Box.TabIndex = 2;
            Output_Text_Box.Text = "";
            // 
            // Output_Label
            // 
            Output_Label.AutoSize = true;
            Output_Label.Location = new Point(19, 260);
            Output_Label.Name = "Output_Label";
            Output_Label.Size = new Size(49, 20);
            Output_Label.TabIndex = 3;
            Output_Label.Text = "Result";
            // 
            // Encrypt_Button
            // 
            Encrypt_Button.Location = new Point(202, 201);
            Encrypt_Button.Name = "Encrypt_Button";
            Encrypt_Button.Size = new Size(75, 41);
            Encrypt_Button.TabIndex = 4;
            Encrypt_Button.Text = "Encrypt";
            Encrypt_Button.UseVisualStyleBackColor = true;
            // 
            // KeyInput_Text_Box
            // 
            KeyInput_Text_Box.Location = new Point(114, 203);
            KeyInput_Text_Box.Name = "KeyInput_Text_Box";
            KeyInput_Text_Box.Size = new Size(72, 39);
            KeyInput_Text_Box.TabIndex = 5;
            KeyInput_Text_Box.Text = "";
            // 
            // Decrypt_Button
            // 
            Decrypt_Button.Location = new Point(293, 201);
            Decrypt_Button.Name = "Decrypt_Button";
            Decrypt_Button.Size = new Size(75, 41);
            Decrypt_Button.TabIndex = 6;
            Decrypt_Button.Text = "Decrypt";
            Decrypt_Button.UseVisualStyleBackColor = true;
            // 
            // Clear_Button
            // 
            Clear_Button.Location = new Point(613, 201);
            Clear_Button.Name = "Clear_Button";
            Clear_Button.Size = new Size(75, 41);
            Clear_Button.TabIndex = 7;
            Clear_Button.Text = "Clear";
            Clear_Button.UseVisualStyleBackColor = true;
            // 
            // Save_Button
            // 
            Save_Button.Location = new Point(516, 201);
            Save_Button.Name = "Save_Button";
            Save_Button.Size = new Size(75, 41);
            Save_Button.TabIndex = 8;
            Save_Button.Text = "Save";
            Save_Button.UseVisualStyleBackColor = true;
            // 
            // Close_Button
            // 
            Close_Button.Location = new Point(707, 201);
            Close_Button.Name = "Close_Button";
            Close_Button.Size = new Size(75, 41);
            Close_Button.TabIndex = 9;
            Close_Button.Text = "Close";
            Close_Button.UseVisualStyleBackColor = true;
            // 
            // Cryptanalysis_Button
            // 
            Cryptanalysis_Button.Location = new Point(389, 201);
            Cryptanalysis_Button.Name = "Cryptanalysis_Button";
            Cryptanalysis_Button.Size = new Size(106, 41);
            Cryptanalysis_Button.TabIndex = 10;
            Cryptanalysis_Button.Text = "Cryptanalysis";
            Cryptanalysis_Button.UseVisualStyleBackColor = true;
            // 
            // Open_Button
            // 
            Open_Button.Location = new Point(19, 201);
            Open_Button.Name = "Open_Button";
            Open_Button.Size = new Size(75, 41);
            Open_Button.TabIndex = 11;
            Open_Button.Text = "Open";
            Open_Button.UseVisualStyleBackColor = true;
            // 
            // Form1
            // 
            AutoScaleDimensions = new SizeF(8F, 20F);
            AutoScaleMode = AutoScaleMode.Font;
            ClientSize = new Size(800, 439);
            Controls.Add(Open_Button);
            Controls.Add(Cryptanalysis_Button);
            Controls.Add(Close_Button);
            Controls.Add(Save_Button);
            Controls.Add(Clear_Button);
            Controls.Add(Decrypt_Button);
            Controls.Add(KeyInput_Text_Box);
            Controls.Add(Encrypt_Button);
            Controls.Add(Output_Label);
            Controls.Add(Output_Text_Box);
            Controls.Add(Input_Label);
            Controls.Add(Input_Text_Box);
            Name = "Form1";
            Text = "Form1";
            ResumeLayout(false);
            PerformLayout();
        }

        #endregion

        private RichTextBox Input_Text_Box;
        private Label Input_Label;
        private RichTextBox Output_Text_Box;
        private Label Output_Label;
        private Button Encrypt_Button;
        private RichTextBox KeyInput_Text_Box;
        private Button Decrypt_Button;
        private Button Clear_Button;
        private Button Save_Button;
        private Button Close_Button;
        private Button Cryptanalysis_Button;
        private Button Open_Button;
    }
}
