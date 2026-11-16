import 'dart:io';

void main(){
  stdout.write("Enter a = "); //print("Enter a = ");
  int a = int.parse(stdin.readLineSync()!);
  stdout.write("Enter b = "); //print("Enter a = ");
  int b = int.parse(stdin.readLineSync()!);

  if (a == 0) {
    if (b == 0) {
      print("Infinite solutions");
    }
    else {
      print("No solution");
    }
  }
  else {
    double x = -b / a;
    print("The solution is x = $x");
  }
}