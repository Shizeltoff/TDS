<?php	
	/**
     * Remplit les jours de la semaine avec les congés de l'utilisateur
     * @param array $conges Liste des congés de l'utilisateur
     * @param array $jours Liste des jours de la période recherchée
     * @param array $types tableau d'association entre l'id du type de congé et son libellé
     * @return array tableau contenant les classes pour chaque jours,ainsi que les infos bulles et les ids des congés et leur état.
     **/
    function fillDays($conges,$jours,$types,$feries,$we=null){
      $types[0]="taf";
      foreach ($jours as $k => $v) {
                $tmp_class[$k] = array('am'=>' am_taf','pm'=>' pm_taf');  
                $ids[$k]['am']='';
                $ids[$k]['pm']='';
                $etat[$k]['am']='';
                $etat[$k]['pm']='';
                $bulles[$k]['am']='';
                $bulles[$k]['pm']='';
              }
      foreach ($conges as $k => $c) {
              if($c->p_etat != 'refus' && $c->p_etat != 'annul'){
                if(in_array($c->p_date_deb, $jours) && in_array($c->p_date_fin, $jours)){
                    //le congé débute et termine dans la semaine
                  $jour_deb = array_search($c->p_date_deb, $jours);
                  $jour_fin = array_search($c->p_date_fin, $jours);
                  if($jour_deb == $jour_fin){               // Congé sur une seule journée
                    if($c->p_demi_jour_deb =='pm'){         // Après-midi
                      // $classe[$jour_deb] = 'pm_'.$types[$c->p_type];
                      $tmp_class[$jour_deb]['pm'] = ' pm_'.$types[$c->p_type];
                      $ids[$jour_deb]['pm'] = $c->p_num;
                      $etat[$jour_deb]['pm'] = $c->p_etat;
                      $bulles[$jour_deb]['pm'] = setTooltip($c);
                    }
                    else{
                      if($c->p_demi_jour_fin =='am'){       // matinée
                        $tmp_class[$jour_deb]['am'] = ' am_'.$types[$c->p_type];
                        $ids[$jour_deb]['am'] = $c->p_num;
                        $etat[$jour_deb]['am'] = $c->p_etat;
                        $bulles[$jour_deb]['am'] = setTooltip($c);
                      }
                      else{                                 //Journée complète
                         $tmp_class[$jour_deb]['am']= ' am_'.$types[$c->p_type];                      
                         $tmp_class[$jour_deb]['pm']= ' pm_'.$types[$c->p_type];                      
                        // $classe[$jour_deb] = $types[$c->p_type];                      
                        $ids[$jour_deb]['am'] = $c->p_num;
                        $etat[$jour_deb]['am'] = $c->p_etat;
                        $ids[$jour_deb]['pm'] = $c->p_num;
                        $etat[$jour_deb]['pm'] = $c->p_etat;
                        $bulles[$jour_deb]['am'] = setTooltip($c);
                      }
                    }
                  }
                  else{                                       //Congé sur plusieurs jours
                    foreach ($jours as $jour => $date) {
                      if(($c->p_date_deb <= $date) && ($date <= $c->p_date_fin)){
                        if($date == $c->p_date_deb){
                          if($c->p_demi_jour_deb =='pm'){           //Débute l'après-midi
                            $tmp_class[$jour]['pm'] = ' pm_'.$types[$c->p_type];
                            $ids[$jour]['pm'] = $c->p_num;
                            $etat[$jour]['pm'] = $c->p_etat;
                            $bulles[$jour]['pm'] = setTooltip($c);
                          }else{
                            $ids[$jour]['am'] = $c->p_num;
                            $ids[$jour]['pm'] = $c->p_num;
                            $etat[$jour]['am'] = $c->p_etat;
                            $etat[$jour]['pm'] = $c->p_etat;
                            $tmp_class[$jour]['am'] = ' am_'.$types[$c->p_type];                      
                            $tmp_class[$jour]['pm'] = ' pm_'.$types[$c->p_type]; 
                            $bulles[$jour]['am'] = setTooltip($c);
                            // $bulles[$jour]['pm'] = setTooltip($c);
                          }
                        }
                        elseif ($date == $c->p_date_fin) {
                          if($c->p_demi_jour_fin =='am'){           // termine fin de matinée
                            $tmp_class[$jour]['am'] =' am_'.$types[$c->p_type];
                            $ids[$jour]['am'] = $c->p_num;
                            $etat[$jour]['am'] = $c->p_etat;
                            $bulles[$jour]['am'] = setTooltip($c);
                          }else{
                            $tmp_class[$jour]['am'] = ' am_'.$types[$c->p_type];                      
                            $tmp_class[$jour]['pm'] = ' pm_'.$types[$c->p_type];
                            $ids[$jour]['am'] = $c->p_num;
                            $etat[$jour]['am'] = $c->p_etat;
                            $ids[$jour]['pm'] = $c->p_num;  
                            $etat[$jour]['pm'] = $c->p_etat;  
                            $bulles[$jour]['am'] = setTooltip($c);
                            // $bulles[$jour]['pm'] = setTooltip($c);
                          }
                        }
                        else{
                          $tmp_class[$jour]['am'] = ' am_'.$types[$c->p_type];                      
                          $tmp_class[$jour]['pm'] = ' pm_'.$types[$c->p_type];
                          $bulles[$jour]['am'] = setTooltip($c);
                          $ids[$jour]['am'] = $c->p_num;
                          $ids[$jour]['pm'] = $c->p_num;
                          // $bulles[$jour]['pm'] = setTooltip($c);
                        }
                      }
                    }
                  }
                }
                elseif(in_array($c->p_date_deb, $jours) && !in_array($c->p_date_fin, $jours)){  // Le congé démarre dans la semaine et fini après
                  $jour_deb = array_search($c->p_date_deb, $jours);
                  foreach ($jours as $jour => $date) {
                    if(($c->p_date_deb <= $date ) && ( $date <= $c->p_date_fin)){ //
                      $tmp_class[$jour]['am'] = ' am_'.$types[$c->p_type];                      
                      $tmp_class[$jour]['pm'] = ' pm_'.$types[$c->p_type];
                      // $classe[$jour] = $types[$c->p_type];
                      $ids[$jour]['am'] = $c->p_num;
                      $etat[$jour]['am'] = $c->p_etat;
                      $ids[$jour]['pm'] = $c->p_num;
                      $etat[$jour]['pm'] = $c->p_etat;
                      $bulles[$jour]['am'] = setTooltip($c);
                      // $bulles[$jour]['pm'] = setTooltip($c);
                    }
                  }
                  if($c->p_demi_jour_deb =='pm'){           //Débute l'après-midi
                    $tmp_class[$jour_deb]['pm'] = ' pm_'.$types[$c->p_type];
                    $bulles[$jour]['pm'] = setTooltip($c);
                    // $classe[$jour_deb] =' pm_'.$types[$c->p_type];
                  }
                }
                elseif(! in_array($c->p_date_deb, $jours) && in_array($c->p_date_fin, $jours)){ // Le congé démarre la semaine d'avant et fini dans la semaine.
                  $jour_fin = array_search($c->p_date_fin, $jours);
                  foreach ($jours as $jour => $date) {
                    if(($c->p_date_deb<=$date)&&($date<=$c->p_date_fin)){ //
                      $tmp_class[$jour]['am'] = ' am_'.$types[$c->p_type];                      
                      $tmp_class[$jour]['pm'] = ' pm_'.$types[$c->p_type];  
                      // $classe[$jour] = $types[$c->p_type];
                      $ids[$jour]['am'] = $c->p_num;
                      $etat[$jour]['am'] = $c->p_etat;
                      $etat[$jour]['pm'] = $c->p_etat;
                      $ids[$jour]['pm'] = $c->p_num;
                      $bulles[$jour]['am'] = setTooltip($c);
                    }
                  }
                  if($c->p_demi_jour_fin =='am'){           // termine fin de matinée
                    $tmp_class[$jour_fin]['am'] = ' am_'.$types[$c->p_type];
                    $bulles[$jour_fin]['am'] = setTooltip($c);
                  }                                
                }
                else{  // Le congé débute et termine hors de la semaine affichée
                  foreach ($jours as $jour => $date) {
                    $tmp_class[$jour]['am'] = ' am_'.$types[$c->p_type];                      
                    $tmp_class[$jour]['pm'] = ' pm_'.$types[$c->p_type];
                    $ids[$jour]['am'] = $c->p_num;
                    $ids[$jour]['pm'] = $c->p_num;
                    $etat[$jour]['am'] = $c->p_etat;
                    $etat[$jour]['pm'] = $c->p_etat;
                    $bulles[$jour]['am'] = setTooltip($c);
                  }
                }
              }
            }
      foreach ($jours as $k => $v) {
            if(in_array($v, $feries)){
            // if(in_array($v, $this->cache->read('ferie'))){
              $classe[$k]="ferie";
            }else{
              $classe[$k] = substr($tmp_class[$k]['am'].$tmp_class[$k]['pm'],1);
            }
            $demi_bulles[$k]['am'] = $bulles[$k]['am'];
            $demi_bulles[$k]['pm'] = $bulles[$k]['pm'];
            if($bulles[$k]['am']=='' && $bulles[$k]['pm'] ==''){
              $bulles[$k]='';

            }else{
              if($bulles[$k]['pm'] ==''){
                $bulles[$k] = '<span>'.$bulles[$k]['am'].'</span>';                               
              }
              elseif($bulles[$k]['am'] ==''){
                $bulles[$k] = '<span>'.$bulles[$k]['pm'].'</span>';                               
              }
              else{
              $bulles[$k] = '<span>'.$bulles[$k]['am'].$bulles[$k]['pm'].'</span>';                               
              }
              
            }
        }
        if ($we) {
          foreach ($we as $key => $value) {
            $classe[$value] = "we";
            // $classe[$value] .= " we";
          }
        }

        $params = array('classe'=>$classe,'demi_classes'=>$tmp_class, 'ids'=>$ids , 'bulles'=>$bulles, 'demi_bulles'=>$demi_bulles,'etat'=>$etat);
        return $params;    
   }