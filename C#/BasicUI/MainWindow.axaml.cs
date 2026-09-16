using Avalonia.Controls;
using Avalonia.Interactivity;
using Avalonia.Media;

namespace BasicUI;

public partial class MainWindow : Window
{
    readonly SolidColorBrush blackColor = new SolidColorBrush(Colors.Black);
    readonly SolidColorBrush whiteColor = new SolidColorBrush(Colors.White);
    
    public MainWindow()
    {
        InitializeComponent();
    }

    private void Button_OnClick(object? sender, RoutedEventArgs e)
    {

        if (this.Background is SolidColorBrush brush && brush == blackColor)
        {
            this.Background = whiteColor;
        }
        else
        {
            this.Background = blackColor;
        }
    }
}