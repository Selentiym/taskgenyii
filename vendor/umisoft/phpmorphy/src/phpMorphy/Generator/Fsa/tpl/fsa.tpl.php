<?php
/*
* This file is part of phpMorphy project
*
* Copyright (c) 2007-2012 Kamaev Vladimir <heromantor@users.sourceforge.net>
*
*     This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2 of the License, or (at your option) any later version.
*
*     This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
*     You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the
* Free Software Foundation, Inc., 59 Temple Place - Suite 330,
* Boston, MA 02111-1307, USA.
*/
 echo '<' . '?' . 'php' . "\n"; ?>
 /**
 * This file is part of phpMorphy library
 *
 * Copyright c 2007-2008 Kamaev Vladimir <heromantor@users.sourceforge.net>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the
 * Free Software Foundation, Inc., 59 Temple Place - Suite 330,
 * Boston, MA 02111-1307, USA.
 */

/**
 * This file is autogenerated at <?php echo date('r')  ?>, don`t change it!
 */
class <?php echo $helper->getClassName() ?> extends <?php echo $helper->getParentClassName() ?> {
    function walk($trans, $word, $readAnnot = true) {
        <?php echo $helper->prolog() ?>;
        
        for($i = 0, $c = $GLOBALS['__phpmorphy_strlen']($word); $i < $c; $i++) {
            $prev_trans = $trans;
            $char = ord($word[$i]);
            
            /////////////////////////////////
            // find char in state begin
<?php echo $helper->tplFindCharInState('$trans', '$char', '$result') ?>
            // find char in state end
            /////////////////////////////////
            
            if(!$result) {
                $trans = $prev_trans;
                break;
            }
        }
        
        $annot = null;
        $result = false;
        $prev_trans = $trans;
        
        if($i >= $c) {
            // Read annotation when we walked all chars in word
            $result = true;
            
            if($readAnnot) {
                // read annot trans
                <?php $helper->out($helper->seekAnnotTrans('$trans'), ';'); ?>
                list(, $trans) = <?php echo $helper->readAnnotTrans('$trans') ?>;
                
                if(0 == <?php echo $helper->checkTerm('$trans'); ?>) {
                    $result = false;
                } else {
                    $annot = $this->getAnnot($trans);
                }
            }
        }
        
        return array(
            'result' => $result,
            'last_trans' => $trans,
            'word_trans' => $prev_trans,
            'walked' => $i,
            'annot' => $annot
        );
    }
    
    function collect($startNode, $callback, $readAnnot = true, $path = '') {
        $total = 0;
        
        $stack = array();
        $stack_idx = array();
        $start_idx = 0;
        array_push($stack, null);
        array_push($stack_idx, null);
        
        $state = $this->readState(<?php echo $helper->getDest('$startNode') ?>);
        
        do {
            for($i = $start_idx, $c = count($state); $i < $c; $i++) {
                $trans = $state[$i];
                
                if(<?php echo $helper->checkTerm('$trans') ?>) {
                    $total++;
                    
                    if($readAnnot) {
                        $annot = $this->getAnnot($trans);
                    } else {
                        $annot = $trans;
                    }
                    
                    if(!call_user_func($callback, $path, $annot)) {
                        return $total;
                    }
                } else {
                    $path .= chr(<?php echo $helper->getChar('$trans') ?>);
                    array_push($stack, $state);
                    array_push($stack_idx, $i + 1);
                    $state = $this->readState(<?php echo $helper->getDest('$trans') ?>);
                    $start_idx = 0;
                    
                    break;
                }
            }
            
            if($i >= $c) {
                $state = array_pop($stack);
                $start_idx = array_pop($stack_idx);
                $path = $GLOBALS['__phpmorphy_substr']($path, 0, -1);
            }
        } while(!empty($stack));
        
        return $total;
    }
    
    function readState($index) {
        <?php echo $helper->prolog() ?>;
        
        $result = array();
        
<?php echo $helper->tplReadState() ?>
        
        return $result;
    }
    
    function unpackTranses($rawTranses) {
        settype($rawTranses, 'array');
        $result = array();
        
        foreach($rawTranses as $rawTrans) {
            $result[] = <?php echo $helper->tplUnpackTrans('$rawTrans') ?>;
        }
        
        return $result;
    }
    
    protected function readRootTrans() {
        <?php echo $helper->prolog() ?>;

        <?php $helper->out($helper->getStorage()->seek($helper->getRootTransOffset()), ';'); ?>
        list(, $trans) = <?php echo $helper->unpackTrans($helper->getStorage()->read($helper->getRootTransOffset(), $helper->getTransSize())) ?>;
        
        return $trans;
    }
    
    protected function readAlphabet() {
        <?php echo $helper->prolog() ?>;
        
<?php $offset = '$this->header[\'alphabet_offset\']' ?>
        <?php $helper->out($helper->getStorage()->seek($offset), ';'); ?>
        return <?php echo $helper->getStorage()->read($offset, '$this->header[\'alphabet_size\']') ?>;
    }
    
    function getAnnot($trans) {
        if(!<?php echo $helper->checkTerm('$trans') ?>) {
            return null;
        }
        
        <?php echo $helper->prolog() ?>;
        
        $offset =
            $this->header['annot_offset'] +
            (<?php echo $helper->getAnnotIdx('$trans') ?>);
        
        <?php echo $helper->out($helper->getStorage()->seek('$offset'), ';') ?>
        $len = ord(<?php echo $helper->getStorage()->read('$offset', 1); ?>);
        
        if($len) {
            $annot = <?php echo $helper->getStorage()->read('$offset + 1', '$len'); ?>;
        } else {
            $annot = null;
        }
        
        return $annot;
    }
    
<?php echo $helper->tplExtraFuncs() ?>
<?php echo $helper->tplExtraProps() ?>
}
