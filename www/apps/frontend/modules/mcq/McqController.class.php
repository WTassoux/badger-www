<?php
    class McqController extends BackController
    {
        ////////////////////////////////////////////////////////////
        /// \brief Execute action Index
        ////////////////////////////////////////////////////////////
        public function executeIndex(HTTPRequest $request)
        {
            $this->page()->addVar('showMCQLink', $this->canTakeMCQ());
        }

        ////////////////////////////////////////////////////////////
        /// \brief Execute action TakeMCQ
        ////////////////////////////////////////////////////////////
        public function executeTakeMCQ(HTTPRequest $request)
        {
            if(!$this->canTakeMCQ())
            {
                // Inclusion of the langage file
                require_once(dirname(__FILE__).'/../../lang/'.$this->app()->user()->getAttribute('vbmifareLang').'.php');

                $this->app()->user()->setFlash($TEXT['Flash_NoTakeMCQ']);
                $this->page()->addVar('showMCQLink', false);
                $this->app()->httpResponse()->redirect('/vbMifare/mcq/index.html');
            }

            $mcqStatus = $this->app()->user()->getAttribute('vbmifareStudent')->getMCQStatus();
            if($mcqStatus != 'Generated')
            {
                // TODO: Mettre Generated pour le user dans la BDD
                $this->app()->user()->getAttribute('vbmifareStudent')->setMCQStatus('Generated');
                $questions = $this->selectQuestions();
            }
            else
                $questions = $this->loadUsersQuestions();

            // Get questions and associated answers
            $answers = $this->getAssociatedAnswers($questions);
            $this->page()->addVar('questions', $questions);
            $this->page()->addVar('answers', $answers);
            $this->page()->addVar('lang', $this->app()->user()->getAttribute('vbmifareLang'));
        }

        ////////////////////////////////////////////////////////////
        /// \brief Can Take the MCQ if correct date or not taken already
        ///
        /// \return Boolean
        ////////////////////////////////////////////////////////////
        private function canTakeMCQ()
        {
            // TODO: Trouver le warning que lance la fonction php date(). Apparement
            // Il faudrait spéficier le fuseau horaire ou quelque chose de la sorte.

            $mcqStatus = $this->app()->user()->getAttribute('vbmifareStudent')->getMCQStatus();

            $startDate = new Date(1,1,1999);
            $currentDate = new Date(1,1,1999);
            $startDate->setFromString($this->app()->configGlobal()->get('MCQStartDate'));
            $currentDate->setFromString(date('d-m-Y'));

            return (in_array($mcqStatus, array('CanTakeMCQ','Generated')) && (Date::compare($currentDate, $startDate) >= 0));
        }

        private function selectQuestions()
        {
            $maxQuestionNumber = $this->app()->configGlobal()->get('MCQMaxQuestions');

            $username = $this->app()->user()->getAttribute('logon');

            $lang = $this->app()->user()->getAttribute('vbmifareLang');

            $managerRegistration = $this->m_managers->getManagerOf('registration');

            $registrations = $managerRegistration->getResgistrationsFromUser($username, 'Present');

            $managerMCQ = $this->m_managers->getManagerOf('mcq');

            // Get obligatory questions
            $questions = array();
            foreach($registrations as $reg)
            {
                $questionOneLecture = $managerMCQ->getQuestionsFromLecture($reg->getId(), $lang, 'Obligatory');
                $questions = array_merge($questions, $questionOneLecture);
            }

            // Enough obligatory questions
            if(count($questions) > $maxQuestionNumber)
            {
                $finalQuestions = array_splice($questions, $maxQuestionNumber);
                $managerMCQ->saveQuestions($this->app()->user()->getAttribute('vbmifareStudent')->getUsername(), $finalQuestions);
                print_r($finalQuestions);
                return $finalQuestions;
            }

            // Count remaining questions to choose and save obligatory ones
            $remaining = $maxQuestionNumber - count($questions);
            $finalQuestions = $questions;

            // Get possible questions
            $questions = array();
            foreach($registrations as $reg)
            {
                $questionsOneLecture = $managerMCQ->getQuestionsFromLecture($reg->getId(), $lang, 'Possible');
                $questions = array_merge($questions, $questionsOneLecture);
            }
            shuffle($questions);
            array_splice($questions, $remaining);

            $result = array_merge($finalQuestions, $questions);
            $managerMCQ->saveQuestions($this->app()->user()->getAttribute('vbmifareStudent')->getUsername(), $result);
            return $result;
        }

        private function loadUsersQuestions()
        {
            $lang = $this->app()->user()->getAttribute('vbmifareLang');

            $managerMCQ = $this->m_managers->getManagerOf('mcq');

            return $managerMCQ->loadQuestions($this->app()->user()->getAttribute('vbmifareStudent')->getUsername(), $lang);
        }

        private function getAssociatedAnswers($questions)
        {
            $lang = $this->app()->user()->getAttribute('vbmifareLang');

            $managerMCQ = $this->m_managers->getManagerOf('mcq');

            $answers = array();
            foreach($questions as $question)
            {
                $answersOneQuestion = $managerMCQ->getAnswersFromQuestion($question->getId(), $lang);
                $answers = array_merge($answers, $answersOneQuestion);
            }

            return $answers;
        }
    }
?>
