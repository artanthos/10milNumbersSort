# 10milNumbersSort
For Media Math challenge

## Prequel

This is not the best of solutions, but it's the one I've come up with.

* I've thought of chunking the large file and running the algo from there on, but reconsidered.
Personal opinion: I think it's best to run a nohup in the cli

* I tried to use the merge sort algo, but couldn't figure out a way to efficiently carry out the operation of chunking the 10mil file and sorting them (the resulting chunks), without saving the 10mil.txt into memory (and thus forcing the server to answer with a 500). I've drafted the algo in JS (you can check out ```mergeSort.js``` file in the bundle), given that I do more JS on a day to day basis than I do PHP. After scouring StackOverflow & phpfreaks algo related topics, I thought about using a merge sort + quicksort algo, but soon gave up on the idea. That's why I didn't translate the JS algo into PHP

* Which brings me to my solution: SQLite. I tested it on a $5 VPS and it took about 4 hours. Yes, it's dead slow, but I'd risk it, given that this operation would run only once in a while. Preferably on a Sunday. At 3am in the morning. On a dev server.

## 1. How to run it

Execute the PHP script in the cli:

```
php sort.php --input /var/www/html/10mil.txt --output /var/www/html/sorted_these_10mil.txt --db yourDbNameHere
```

* ```--input``` is the input file (e.g. 10mil.txt, along with the absolute path where it resides).
* ```--output``` is the output file with the sorted numbers from 10mil.txt (e.g. sorted_these_10mil.txt, along with the absolute path where it resides).
* ```--db``` is the name of your DB file, which will be saved in the same place with your ```sort.php``` script

NOTE: I've made all arguments mandatory. If you want to change this requirement, look at the 

```php
private function setIOFromCLIEnvironment()
``` 

After the ```sort.php``` finishes executing, it'll generate ```sorted_these_10mil.txt``` (or how you decided to call your output file). Now run this command to chunk it down:

```split --bytes=10M /var/www/html/sorted_these_10mil.txt /var/www/html/chunks/part_```

* The ```--bytes``` argument is how large you want each chunk to be
* The ```/var/www/html/sorted_these_10mil.txt``` argument is the file you want to chunk down
* The ```/var/www/html/chunks/part_``` is the destination and how you want your chunks to be named.
The above command will give you something along the lines of ```part_aa, part_ab, part_ac, part_ad``` and so on.

## 2. How it's made

Instantiate the ```mergeSortNulled``` class

```php
$sort = new mergeSortNulled();
```

The ```__constructor()``` method validates your CLI variables.

If you forgot one argument, the CLI prompts you with an error message and the script stops executing.

```$this->setIOFromCLIEnvironment()``` sets off both the validation and setters for your CLI input variables.
I could've done it more sophisticated, but given that I made all vars mandatory, I didn't pursue this route any further.
Basically, if you don't set all 3 vars (```--input```, ```--output``` and ```--db```), you'll populate the ```$this->CLIErrorForThis``` array.

You next invoke its two public methods:
```php
$sort->importData();
$sort->exportData();
```

Each method is pretty straight forward.

```importData()``` does 4 things:
* Calls ```checkIfCLISetDbElseEXIT()``` and creates the ```numbers``` table (with one ```value``` column - integer type) if ```--db``` is set from the CLI
* opens ```$this->fileToReadFrom``` with reading permissions
* chunks it down and
* inserts its contents into the ```numbers``` table

```exportData()``` does 3 things:
* opens the ```$this->fileToWriteTo```, namely ```sorted_these_10mil.txt``` with write permissions
* does an ```order by value```
* dumps the result in the ```sorted_these_10mil.txt``` file

