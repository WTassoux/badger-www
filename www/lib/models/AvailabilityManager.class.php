<?php
    class AvailabilityManager extends Manager
    {
        public function get($id = -1)
        {
            $requestSQL = 'SELECT Id_availability,
                                  Id_classroom,
                                  Date,
                                  StartTime,
                                  EndTime FROM Availabilities';

            if($id != -1)
                $requestSQL .= ' WHERE Id_availability = ' . $id;

            $req = $this->m_dao->prepare($requestSQL);
            $req->execute(); 

            $availabilities = array();

            while($data = $req->fetch())
            {
                $date = new Date;
                $date->setFromMySQLResult($data['Date']);

                $startTime = new Time;
                $startTime->setFromString($data['StartTime']);

                $endTime = new Time;
                $endTime->setFromString($data['EndTime']);

                $availability = new Availability;
                $availability->setId($data['Id_availability']);
                $availability->setIdClassroom($data['Id_classroom']);
                $availability->setDate($date);
                $availability->setStartTime($startTime);
                $availability->setEndTime($endTime);

                $availabilities[] = $availability;
            }

            return $availabilities;
        }

        public function save($availabilities)
        {
            $req = $this->m_dao->prepare('INSERT INTO Availabilities(Id_classroom,
                                                                     Date, 
                                                                     StartTime,
                                                                     EndTime) VALUES(?, ?, ?, ?)');

            foreach($availabilities as $availability)
            {
                $req->execute(array($availability->getIdClassroom(),
                                    $availability->getDate()->toStringMySQL(),
                                    $availability->getStartTime()->toStringMySQL(),
                                    $availability->getEndTime()->toStringMySQL()));
            }
        }

       public function update($availability)
        {
            $req = $this->m_dao->prepare('UPDATE Availabilities SET Date = ?, 
                                                                    StartTime = ?,
                                                                    EndTime = ? WHERE Id_availability = ?');

            $req->execute(array($availability->getDate()->toStringMySQL(),
                                $availability->getStartTime()->toStringMySQL(),
                                $availability->getEndTime()->toStringMySQL(),
                                $availability->getId()));
        } 
    }
?>