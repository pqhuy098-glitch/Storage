public class Math
{
    public long Sum (int a, int b)
    {
        return a + b;
    }

    public long Minus (int a, int b)
    {
        return a - b;
    }

    public long Multiply (int a, int b)
    {
        return a * b;
    }

    public float Divide (float a, float b)
    {
        if (b == 0.0)
        {
            Console.WriteLine("b must not be equal 0");
        }
        return a/b;
    }
}