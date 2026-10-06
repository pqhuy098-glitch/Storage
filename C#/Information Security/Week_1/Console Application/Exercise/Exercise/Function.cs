using System;
using System.Collections.Generic;
using System.Text;

namespace Exercise
{
    internal class Function
    {
        private bool isLeap = false;
        private int days;

        public void Exercise_1 (int year)
        {
            if (year % 100 == 0)
            {
                if (year % 400 == 0)
                {
                    Console.WriteLine("It's a leap year");
                    isLeap = true;
                }
                else
                {
                    Console.WriteLine("It's not a leap year");
                }
            }
            else
            {
                if (year % 4 == 0)
                {
                    Console.WriteLine("It's a leap year");
                    isLeap = true;
                }
                else
                {
                    Console.WriteLine("It's not a leap year");
                }
            }
        }

        public void Exercise_2 (double a, double b, double c)
        {
            double delta = b * b - 4 * a * c;
            double x1 = 0;
            double x2 = 0;

            if (delta < 0)
            {
                Console.WriteLine("This equation with no solution");
            }
            else if (delta == 0)
            {
                x1 = -(b / (2 * a));

                Console.WriteLine("This equation has a double root x1 = x2 = " +  x1);
            }
            else
            {
                x1 = (-b + Math.Sqrt(delta)) / (2 * a);
                x2 = (-b - Math.Sqrt(delta)) / (2 * a);

                Console.WriteLine("the equation has 2 distinct solutions");
                Console.WriteLine("x1 = " + x1);
                Console.WriteLine("x2 = " + x2);
            }
        }

        public void Exercise_3 (int month, int year)
        {
            switch (month)
            {
                case 1: case 3: case 5: case 7: case 8: case 10: case 12:
                    Console.WriteLine(month + " has " + 31 + " days");
                    days = 31;
                    break;
                case 4: case 6: case 9: case 11:
                    Console.WriteLine(month + " has " + 30 + " days");
                    days = 30;
                    break;
                case 2:
                    Exercise_1(year);
                    
                    if (isLeap)
                    {
                        Console.WriteLine(month + " has " + 29 + " days");
                        days = 29;
                    }
                    else
                    {
                        Console.WriteLine(month + " has " + 28 + " days");
                        days = 28;
                    }
                    break;    
            }
        }

        public void Exercise_4 (float score)
        {
            if (score >= 8.5)
            {
                Console.WriteLine("Score A");
            }
            else if (score >= 7.0)
            {
                Console.WriteLine("Score B");
            } 
            else if (score >= 5.5)
            {
                Console.WriteLine("Score C");
            }
            else if (score >= 4.0)
            {
                Console.WriteLine("Score D");
            }
            else
            {
                Console.WriteLine("Score F");
            }
        }

        public void Exercise_5 (float oldElec, float newElec)
        {
            if (oldElec <= newElec)
            {
                var kWh = newElec - oldElec;
                var cost = 0.0f;


                if (kWh > 300)
                {
                    cost += (kWh - 300) * 2503;
                    kWh = 300;
                }
                if (kWh > 200)
                {
                    cost += (kWh - 200) * 2242;
                    kWh = 200;
                }
                if (kWh > 100)
                {
                    cost += (kWh - 100) * 1786;
                    kWh = 100;
                }
                if (kWh > 50)
                {
                    cost += (kWh - 50) * 1533;
                    kWh = 50;
                }
                if (kWh > 0)
                {
                    cost += kWh * 1480;

                }

                Console.WriteLine("Bill Electric is: " + cost);
            }
        }

        public void Exercise_6 (int day, string type)
        {
            double cost = 0;

            switch (type)
            {
                case "A":
                    cost = 500000 * day;

                    if (day > 7)
                    {
                        cost = cost * (1 - 0.1);
                        Console.WriteLine("Cost: " + cost);
                    }
                    else
                    {
                        Console.WriteLine("Cost: " + cost);
                    }
                break;

                case "B":
                    cost = 400000 * day;

                    if (day > 7)
                    {
                        cost = cost * (1 - 0.08);
                        Console.WriteLine("Cost: " + cost);
                    }
                    else
                    {
                        Console.WriteLine("Cost: " + cost);
                    }
                    break;

                case "C":
                    cost = 400000 * day;

                    if (day > 7)
                    {
                        cost = cost * (1 - 0.08);
                        Console.WriteLine("Cost: " + cost);
                    }
                    else
                    {
                        Console.WriteLine("Cost: " + cost);
                    }
                    break;
            }
        }

        public void Exercise_7 (int day, int month, int year)
        {
            if (month > 0 && month < 13)
            {
                Console.WriteLine("Month is valid");
            }
            else
            {
                Console.WriteLine("Month is not valid");
            }

            if (year > 0)
            {
                Console.WriteLine("Year is valid");
            }
            else
            {
                Console.WriteLine("Year is not valid");
            }

            Exercise_3(month, year);

            if (day > 0 && day <= days)
            {
                Console.WriteLine("Day is valid");
            }
            else
            {
                Console.WriteLine("Day is not valid");
            }
        }

        public void Exercise_8 (int day, int month, int year)
        {
            int preDay = day - 1;
            int preMonth = month;
            int preYear = year;
            int nextDay = day + 1;
            int nextMonth = month;
            int nextYear = year;

            Exercise_3(month, year);

            if (preDay < 1)
            {
                preMonth = month - 1;
                Exercise_3(preMonth, year);
                preDay = days;
                
                if (preMonth < 1)
                {
                    preMonth = 12;
                    preYear = year - 1;
                }
            }

            Exercise_3(month, year);

            if (nextDay > days)
            {
                nextDay = 1;
                nextMonth = month + 1;
                
                if (nextMonth > 12)
                {
                    nextMonth = 1;
                    nextYear = year + 1;
                }
            }

            Console.WriteLine("Result");
            Console.WriteLine("Previous day: " + preDay);
            Console.WriteLine("Previous month: " + preMonth);
            Console.WriteLine("Previous year: " + preYear);
            Console.WriteLine("Next day: " + nextDay);
            Console.WriteLine("Next month: " + nextMonth);
            Console.WriteLine("Next year: " + nextYear);
        }

        public void Exercise_9 (int x)
        {
            bool isPrime = true;

            if (x < 2)
            {
                isPrime = false;
            }
            else
            {
                for (int i = 2; i <= Math.Sqrt(x); i++)
                {
                    if (x % i == 0)
                    {
                        isPrime = false;
                        break;
                    }
                }
            }


            if (isPrime)
            {
                Console.WriteLine(x + " is a prime");
            }
            else
            {
                Console.WriteLine(x + " is not a prime");
            }
        }


    }
}
