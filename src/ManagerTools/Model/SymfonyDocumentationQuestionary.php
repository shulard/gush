<?php

/*
 * This file is part of the Manager Tools.
 *
 * (c) Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace ManagerTools\Model;

class SymfonyDocumentationQuestionary implements Questionary
{
    public function getQuestions()
    {
        $questionArray = array(
            array('Doc fix?', 'yes|no'),
            array('New docs?', 'yes|no'),
            array('Applies to', '2.3+'),
            array('Fixed tickets', '#000'),
            array('License', 'CC-ASA 3.0 Unported'),
        );

        $questions = array();

        foreach ($questionArray as $statement) {
            $questions[] = new Question($statement);
        }

        return $questions;
    }

    public function getHeaders()
    {
        return array('Q', 'A');
    }
}
