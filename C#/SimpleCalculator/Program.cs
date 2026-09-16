public class Program
{
    public static void Main()
    {
        var result = new Math();

        Console.WriteLine("sum: " + result.Sum(4, 6));
        Console.WriteLine("minus: " + result.Minus(10, 8));
        Console.WriteLine("minus: " + result.Multiply(2, 3));
        Console.WriteLine("minus: " + result.Divide(3, 7));
    }
}