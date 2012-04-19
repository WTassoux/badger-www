<?php
    class DocumentOfUser extends Record
    {
        private $m_idPackage;
        private $m_idUser;
        private $m_filename;

        public function setIdPackage($idPackage)
        {
            $this->m_idPackage = $idPackage;
        }

        public function getIdPackage()
        {
            return $this->m_idPackage;
        }

        public function setIdUser($idUser)
        {
            $this->m_idUser = $idUser;
        }

        public function getIdUser()
        {
            return $this->m_idUser;
        }

        public function setFilename($filename)
        {
            $this->m_filename = $filename;
        }

        public function getFilename()
        {
            return $this->m_filename;
        }
    }
?>
