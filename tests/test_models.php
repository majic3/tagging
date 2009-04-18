<?php
class Article extends AppModel {
    var $name = 'Article';
    var $actsAs = array('Tagging.Taggable');
}