import 'dart:math';
import 'dart:io';

void main(){
  Random r = Random();
  int n = r.nextInt(100) + 1;
  int guess = 0;

  stdout.write("Guess the number: ");
  guess = int.parse(stdin.readLineSync()!);

  if (guess > n) print('Greater than');
  else if (guess < n) print('Less than');
  else print("Exactly!");

}