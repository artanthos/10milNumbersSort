<?php
    class mergeSortNulled
    {
        private $argsFromCLI;
        private $CLIErrorForThis;
        private $fileToReadFrom;
        private $fileToWriteTo;
        private $db;
        
        public function __construct()
        {
            $this->setIOFromCLIEnvironment();
        }
        
            //CLI related
            private function setIOFromCLIEnvironment()
            {
                $shortOpts  = 'i:o:d:';
                $IOtypes = array('input', 'output', 'db');
                
                foreach ($IOtypes as $IOtype) {
                    $longOpts[] = $IOtype.':';
                }
                
                $this->argsFromCLI = getopt($shortOpts, $longOpts);
                
                foreach ($IOtypes as $IOtype) {
                    $this->seeIfCLIProvidedThis($IOtype);
                    if (!isset($this->CLIErrorForThis[$IOtype])) {
                        $this->letsProceedAndSetThis($IOtype);
                    } else {
                        print $this->CLIErrorForThis[$IOtype];
                    }
                }
            }
                
        private function seeIfCLIProvidedThis($IOtype)
        {
            try {
                $this->checkForThis($IOtype);
            } catch (Exception $e) {
                $this->CLIErrorForThis[$IOtype] = $e->getMessage();
            }
        }
                
        private function checkForThis($IOtype)
        {
            if (!isset($this->argsFromCLI[$IOtype])) {
                throw new InvalidArgumentException("\n --{$IOtype} arg required\n");
            }
        }
        
        private function letsProceedAndSetThis($IOtype)
        {
            switch ($IOtype) {
                    case 'input':
                        $this->fileToReadFrom = $this->argsFromCLI[$IOtype];
                        break;
                    case 'output':
                        $this->fileToWriteTo = $this->argsFromCLI[$IOtype];
                        break;
                    case 'db':
                        $this->db = new SQLite3($IOtype);
                        break;
                }
        }
        
        // DB related
        public function importData()
        {
            $this->checkIfCLISetDbElseEXIT();

            $inputFileHandler = fopen($this->fileToReadFrom, "r");
            $lastExplodedNumberFromPreviousChunkFile = null;
            if ($inputFileHandler) {
                while (($thisChunkFile = fgets($inputFileHandler, 2048)) !== false) {
                    $numbersArrayInChunk = explode(',', $thisChunkFile);
                    if ($lastExplodedNumberFromPreviousChunkFile !== null) {
                        $numbersArrayInChunk[0] = (int) $lastExplodedNumberFromPreviousChunkFile . $numbersArrayInChunk[0];
                    }
                    $lastExplodedNumberFromPreviousChunkFile = array_pop($numbersArrayInChunk);
                    $this->writeToDb($numbersArrayInChunk);
                }
                
                if (!feof($inputFileHandler)) {
                    print "Error: unexpected fgets() fail\n";
                }
                
                fclose($inputFileHandler);
            }
        }
        private function checkIfCLISetDbElseEXIT()
        {
            if ($this->db===null) {
                exit;
            }
                
            $this->db->exec('DROP TABLE IF EXISTS numbers');
            $this->db->exec('CREATE TABLE numbers (value INTEGER)');
        }
        
        private function writeToDb($numbers)
        {
            foreach ($numbers as $number) {
                $this->db->exec('INSERT INTO numbers VALUES (' . $number . ')');
            }
        }

        public function exportData()
        {
            $outputFileHandler = @fopen($this->fileToWriteTo, "w");
            $results = $this->db->query('SELECT * FROM numbers ORDER BY value');
            while ($row = $results->fetchArray()) {
                fwrite($outputFileHandler, $row['value'] . ',');
            }
            fclose($outputFileHandler);
        }
    }

    $sort = new mergeSortNulled();
    $sort->importData();
    $sort->exportData();
