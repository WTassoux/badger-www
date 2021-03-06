<?php
    class Package extends Record
    {
        private $m_idPackage;
        private $m_lecturer;
        private $m_name;
        private $m_description;

        public function setId($idPackage)
        {
            $this->m_idPackage = $idPackage;
        }

        public function getId()
        {
            return $this->m_idPackage;
        }

        public function setLecturer($lecturer)
        {
            $this->m_lecturer = $lecturer;
        }

        public function getLecturer()
        {
            return $this->m_lecturer;
        }

        public function setName($lang, $name)
        {
            $this->m_name[$lang] = $name;
        }

        public function getName($lang)
        {
            return $this->m_name[$lang];
        }

        public function setDescription($lang, $description)
        {
            $this->m_description[$lang] = $description;
        }

        public function getDescription($lang)
        {
            return $this->m_description[$lang];
        }
    }
?>
