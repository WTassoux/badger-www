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
            $logon = $this->app()->user()->getAttribute('logon');

            if(!$this->canTakeMCQ())
            {
                // Inclusion of the langage file
                require_once(dirname(__FILE__).'/../../lang/'.$this->app()->user()->getAttribute('vbmifareLang').'.php');

                $this->app()->user()->setFlashError($TEXT['Flash_NoTakeMCQ']);
                $this->page()->addVar('showMCQLink', false);
                $this->app()->httpResponse()->redirect('/vbMifare/mcq/index.html');
            }

            $managerUser = $this->m_managers->getManagerOf('user');

            $mcqStatus = $this->app()->user()->getAttribute('vbmifareStudent')->getMCQStatus();
            if($mcqStatus != 'Generated')
            {
                $managerUser->updateStatus($this->app()->user()->getAttribute('logon'), 'Generated');
                $this->app()->user()->getAttribute('vbmifareStudent')->setMCQStatus('Generated');

                $questions = $this->selectQuestions();
            }

            else
                $questions = $this->loadUsersQuestions();

            // Get questions and associated answers
            $answers = $this->getAssociatedAnswers($questions);

            // If the user has validated the mcq
            if($request->postExists('isSubmitted'))
            {
                $answersOfUser = $this->computeAndSaveUserAnswers($request, $logon, $answers);

                // Update the user status
                $managerUser->updateStatus($logon, 'Taken');
                $this->app()->user()->getAttribute('vbmifareStudent')->setMCQStatus('Taken');

                $mark = $this->computeMark($logon, $answers, $answersOfUser);
                $managerUser->updateMark($logon, $mark);
                $this->app()->user()->getAttribute('vbmifareStudent')->setMark($mark);

                // Inclusion of the langage file
                require_once(dirname(__FILE__).'/../../lang/'.$this->app()->user()->getAttribute('vbmifareLang').'.php');

                // Redirection
                $this->app()->user()->setFlashInfo($TEXT['Flash_MCQTaken']);
                $this->app()->httpResponse()->redirect('/vbMifare/home/index.html');
            }

            // Else display the form
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
            $student = $this->app()->user()->getAttribute('vbmifareStudent');

            $department = $student->getDepartment();
            $schoolYear = $student->getSchoolYear();
            $mcqStatus = $student->getMCQStatus();

            $managerMCQ = $this->m_managers->getManagerOf('mcq');
            $mcqs = $managerMCQ->get($department, $schoolYear);

            $goodDate = false;
            $goodTime = false;

            date_default_timezone_set('Europe/Paris');
            $currentDate = new Date;
            $currentDate->setFromString(date('d-m-Y'));

            $currentTime = new Time;
            $currentTime->setFromString(date('H:i:s'));

            foreach($mcqs as $mcq)
            {
                if(Date::compare($currentDate, $mcq->getDate()) == 0)
                    $goodDate = true;

                if( (Time::compare($currentTime, $mcq->getStartTime()) >= 0) &&
                    (Time::compare($currentTime, $mcq->getEndTime()) == -1) )
                    $goodTime = true;
            }

            return (in_array($mcqStatus, array('CanTakeMCQ','Generated')) && $goodDate && $goodTime);
        }

        private function selectQuestions()
        {
            $maxQuestionNumber = $this->m_managers->getManagerOf('config')->get('MCQMaxQuestions');

            $username = $this->app()->user()->getAttribute('logon');

            $lang = $this->app()->user()->getAttribute('vbmifareLang');

            $managerRegistration = $this->m_managers->getManagerOf('registration');

            $registrations = $managerRegistration->getResgistrationsFromUser($username);

            $managerMCQ = $this->m_managers->getManagerOf('mcq');

            // Get obligatory questions
            $questions = array();
            $packagesIdDo = array();
            foreach($registrations as $reg)
            {
                $id = $reg->getIdPackage();

                if(! in_array($id, $packagesIdDo) )
                {
                    $packagesIdDo[] = $id;      

                    $questionOneLecture = $managerMCQ->getQuestionsFromPackage($id, 'Obligatory');
                    $questions = array_merge($questions, $questionOneLecture);
                }
            }

            // Enough obligatory questions
            if(count($questions) > $maxQuestionNumber)
            {
                $finalQuestions = array_splice($questions, $maxQuestionNumber);
                $managerMCQ->saveQuestionsOfUser($this->app()->user()->getAttribute('vbmifareStudent')->getUsername(), $finalQuestions);
                print_r($finalQuestions);
                return $finalQuestions;
            }

            // Count remaining questions to choose and save obligatory ones
            $remaining = $maxQuestionNumber - count($questions);
            $finalQuestions = $questions;

            // Get possible questions
            $questions = array();
            $packagesIdDo = array();
            foreach($registrations as $reg)
            {
                $id = $reg->getIdPackage();

                if(! in_array($id, $packagesIdDo) )
                {
                    $packagesIdDo[] = $id;      

                    $questionsOneLecture = $managerMCQ->getQuestionsFromPackage($id, 'Possible');
                    $questions = array_merge($questions, $questionsOneLecture);
                }
            }
            shuffle($questions);
            array_splice($questions, $remaining);

            $result = array_merge($finalQuestions, $questions);
            $managerMCQ->saveQuestionsOfUser($this->app()->user()->getAttribute('vbmifareStudent')->getUsername(), $result);
            return $result;
        }

        private function loadUsersQuestions()
        {
            $lang = $this->app()->user()->getAttribute('vbmifareLang');

            $managerMCQ = $this->m_managers->getManagerOf('mcq');

            return $managerMCQ->loadQuestionsOfUser($this->app()->user()->getAttribute('vbmifareStudent')->getUsername());
        }

        private function computeAndSaveUserAnswers(HTTPRequest $request, $logon, $answersInForm)
        {
            $answersOfUser = array();

            // Retrieve answers of user from the answers in the form
            foreach($answersInForm as $answer)
            {
                if($request->postExists($answer->getId()))
                {
                    $answerOfUser = new AnswerOfUser;
                    $answerOfUser->setIdUser($logon);
                    $answerOfUser->setIdQuestion($answer->getIdQuestion());
                    $answerOfUser->setIdAnswer($answer->getId());

                    array_push($answersOfUser, $answerOfUser);
                }
            }

            // Save them
            $managerMcq = $this->m_managers->getManagerOf('mcq');
            $managerMcq->saveAnswersOfUser($answersOfUser);

            return $answersOfUser;
        }

        private function getAssociatedAnswers($questions)
        {
            $lang = $this->app()->user()->getAttribute('vbmifareLang');

            $managerMCQ = $this->m_managers->getManagerOf('mcq');

            $answers = array();
            foreach($questions as $question)
            {
                $answersOneQuestion = $managerMCQ->getAnswersFromQuestion($question->getId());
                $answers = array_merge($answers, $answersOneQuestion);
            }

            shuffle($answers);
            return $answers;
        }

        public function computeMark($logon, $answers, $answersOfUser)
        {
            $mark = 0;
    
            $presentMark = $this->m_managers->getManagerOf('config')->get('presentMark');

            $managerRegistration = $this->m_managers->getManagerOf('registration');
            $registrations = $managerRegistration->getResgistrationsFromUser($logon);

            foreach($registrations as $reg)
            {
                if($reg->getStatus() == 'Present')
                    $mark += $presentMark / count($registrations);
            }

            $remainingPoints = 20 - $presentMark;
            $goodAnswersCount = 0;
            $badAnswersCount = 0;

            foreach($answers as $answer)
            {
                if($answer->getTrueOrFalse() == 'T')
                    $goodAnswersCount++;

                else
                    $badAnswersCount++;
            }

            foreach($answersOfUser as $answerOfUser)
            {
                $goodAnswer;

                foreach($answers as $answer)
                {
                    if($answerOfUser->getIdAnswer() == $answer->getId())
                    {
                        $goodAnswer = ($answer->getTrueOrFalse() == 'T') ? true : false;
                    }
                }

                if($goodAnswer)
                    $mark += $remainingPoints / $goodAnswersCount;

                else
                    $mark -= $remainingPoints / $badAnswersCount;
            }

            return $mark;
        }
    }
?>
