<?php
    class Student extends Record
    {
        private $m_username;
        private $m_departement; 
        private $m_active;
        private $m_statut;
        private $m_schoolYear;
        private $m_mifare;

        private $m_mcqStatus;
        private $m_mark;

        public function setUsername($username)
        {
            $this->m_username = $username;
        }

        public function getUsername()
        {
            return $this->m_username;
        }

        public function setDepartment($department)
        {
            $this->m_department = $department;
        }

        public function getDepartment()
        {
            return $this->m_department;
        }

        public function setActive($active)
        {
            $this->m_active = $active;
        }

        public function getActive()
        {
            return $this->m_active;
        }

        public function setStatus($status)
        {
            $this->m_status = $status;
        }

        public function getStatus()
        {
            return $this->m_status;
        }

        public function setSchoolYear($schoolYear)
        {
            $this->m_schoolYear = $schoolYear;
        }

        public function getSchoolYear()
        {
            return $this->m_schoolYear;
        }

        public function setMifare($mifare)
        {
            $this->m_mifare = $mifare;
        }

        public function setMark($mark)
        {
            $this->m_mark = $mark;
        }

        public function getMark()
        {
            return $this->m_mark;
        }

        public function setMCQStatus($mcqStatus)
        {
            $this->m_mcqStatus = $mcqStatus;
        }

        public function getMCQStatus()
        {
            return $this->m_mcqStatus;
        }
    }
?>
