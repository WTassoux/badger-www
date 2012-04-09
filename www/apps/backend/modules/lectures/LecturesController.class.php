<?php
    class LecturesController extends BackController
    {
        public function executeIndex(HTTPRequest $request)
        {

        }

        public function executeAddPackages(HTTPRequest $request)
        {
            if ($request->fileExists('vbmifarePackagesCSV'))
            {
                $fileData = $request->fileData('vbmifarePackagesCSV');

                if($fileData['error'] == 0)
                {
                    $file = fopen($fileData['tmp_name'], 'r');

                    $packages = array();

                    while (($lineDatas = fgetcsv($file)) !== FALSE) 
                    {
                        if(count($lineDatas) != 5)
                        {
                            $this->app()->user()->setFlash('File has not got 5 rows');
                            $this->app()->httpResponse()->redirect('./addPackages.html');
                            break;
                        }
        
                        $package = new Package;
                        $package->setLecturer($lineDatas[0]);
                        $package->setNameFr($lineDatas[1]);
                        $package->setNameEn($lineDatas[2]);
                        $package->setDescriptionFr($lineDatas[3]);
                        $package->setDescriptionEn($lineDatas[4]);

                        array_push($packages, $package);
                    }

                    fclose($file);

                    $manager = $this->m_managers->getManagerOf('package');
                    $manager->save($packages);

                    $this->app()->user()->setFlash('File uploaded');
                }

                else
                    $this->app()->user()->setFlash('Error during the upload of packages');
            }
        }


        public function executeAddLecturesAndQuestionsAnswers(HTTPRequest $request)
        {
            // Create a flash message because we can have more than one message.
            $flashMessage = '';

            // Upload lectures for a package
            if($request->fileExists('vbmifareLecturesCSV'))
            {
                $fileData = $request->fileData('vbmifareLecturesCSV');

                if($fileData['error'] == 0)
                {
                    $file = fopen($fileData['tmp_name'], 'r');

                    $lectures = array();

                    while (($lineDatas = fgetcsv($file)) !== FALSE) 
                    {
                        if(count($lineDatas) != 7)
                        {
                            $this->app()->user()->setFlash('Lecture csv file has not got 7 rows.');
                            $this->app()->httpResponse()->redirect('./addLecturesAndQuestionsAnswers.html');
                        }
        
                        $lecture = new Lecture;
                        $lecture->setIdPackage($request->postData('vbmifarePackage'));
                        $lecture->setNameFr($lineDatas[0]);
                        $lecture->setNameEn($lineDatas[1]);
                        $lecture->setDescriptionFr($lineDatas[2]);
                        $lecture->setDescriptionEn($lineDatas[3]);
                        $lecture->setDate($lineDatas[4]);
                        $lecture->setStartTime($lineDatas[5]);
                        $lecture->setEndTime($lineDatas[6]);

                        array_push($lectures, $lecture);
                    }

                    fclose($file);

                    $managerLectures = $this->m_managers->getManagerOf('lecture');
                    $managerLectures->save($lectures);

                    $flashMessage = 'Lectures uploaded.';
                }

                else
                    $flashMessage = 'Cannot upload lectures.';
            }


            // Upload questions/answers for a package
            if($request->fileExists('vbmifareQuestionsAnswersCSV'))
            {
                $fileData = $request->fileData('vbmifareQuestionsAnswersCSV');

                if($fileData['error'] == 0)
                {
                    $file = fopen($fileData['tmp_name'], 'r');

                    $questions = array();
                    $answers = array();

                    // Processing here...
                    $flashMessage .= 'Upload questions/answers is not implemented yet.';

                    fclose($file);

                    $managerMCQ = $this->m_managers->getManagerOf('mcq');
                    $managerMCQ->saveQuestions($questions);
                    $managerMCQ->saveAnswers($answers);

                    $flashMessage .= 'Questions/answers uploaded.';
                }

                else
                    $flashMessage .= 'Cannot upload questions/answers.';
            }


            // Else display the form
            $lang = $this->app()->user()->getAttribute('vbmifareLang');

            $managerPackages = $this->m_managers->getManagerOf('package');
            $packages = $managerPackages->get($lang);

            if( count($packages) == 0)
            {
                $this->app()->user()->setFlash('You need at least a package to upload Lectures or Questions/Answers.');
                $this->app()->httpResponse()->redirect('/vbMifare/admin/lectures/index.html');
            }

            $this->app()->user()->setFlash($flashMessage); 

            $this->page()->addVar('packages', $packages);
        }
    }
?>
