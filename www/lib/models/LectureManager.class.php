<?php
    class LectureManager extends Manager
    {
        public function get($lang, $idPackage = -1)
        {
            $methodName = 'setName'.ucfirst($lang);
            $methodDescription = 'setDescription'.ucfirst($lang);

            $requestSQL = 'SELECT Id_lecture,
                                  Id_package,
                                  Id_availability, 
                                  Name_'.$lang.', 
                                  Description_'.$lang.', 
                                  Date,
                                  StartTime,
                                  EndTime FROM Lectures';

            if($idPackage != -1)
                $requestSQL .= ' WHERE Id_package = ' . $idPackage;

            $req = $this->m_dao->prepare($requestSQL);
            $req->execute(); 

            $lectures = array();

            while($data = $req->fetch())
            {
                $lecture = new Lecture;
                $lecture->setId($data['Id_lecture']);
                $lecture->setIdPackage($data['Id_package']);
                $lecture->setIdAvailability($data['Id_availability']);
                $lecture->$methodName($data['Name_'.$lang]);
                $lecture->$methodDescription($data['Description_'.$lang]);
                $lecture->setDate($data['Date']);
                $lecture->setStartTime($data['StartTime']);
                $lecture->setEndTime($data['EndTime']);

                $lectures[] = $lecture;
            }

            return $lectures;
        }

        public function save($lectures)
        {
            $req = $this->m_dao->prepare('INSERT INTO Lectures(Id_package,
                                                               Id_availability, 
                                                               Name_fr, 
                                                               Name_en, 
                                                               Description_fr,
                                                               Description_en,
                                                               Date,
                                                               StartTime,
                                                               EndTime) VALUES(?, 0, ?, ?, ?, ?, ?, ?, ?)');

            foreach($lectures as $lecture)
                $req->execute(array($lecture->getIdPackage(),
                                    $lecture->getNameFr(),
                                    $lecture->getNameEn(),
                                    $lecture->getDescriptionFr(),
                                    $lecture->getDescriptionEn(),
                                    $lecture->getDate(),
                                    $lecture->getStartTime(),
                                    $lecture->getEndTime()));
        }
    }
?>
