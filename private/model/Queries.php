<?php

	class Queries extends Model{

		public $queries = array(
				'default'=>'select u_login,u_nom,u_prenom,u_is_resp,u_resp_login,u_is_admin,u_see_all,u_quotite,u_email,u_num_exercice from conges_users',
				'user_group'=>'select gu_gid from conges_groupe_users',
				'group_users'=>'select 
						gu_login,
						u_login,u_nom,u_prenom,u_is_resp,u_resp_login,u_is_admin,u_see_all,u_quotite,u_email,u_num_exercice
					from conges_groupe_users
					left join conges_users
						on conges_groupe_users.gu_login = conges_users.u_login
				',
				'group_name'=>'select * from conges_groupe',
				'responsables'=>'select
						gr_login,
						u_email,
						u_nom,
						u_prenom
					from
						conges_groupe_resp
					left join conges_users
						on conges_groupe_resp.gr_login =  conges_users.u_login
				',
				'resp_mail'=>'select u_email from conges_users',
				'solde_user'=>'select * from conges_solde_user',
				'solde'=>'select
						ta_libelle,
						su_nb_an,su_solde,su_reliquat
			  		from conges_type_absence
			  		left join conges_solde_user
			  			on conges_type_absence.ta_id = conges_solde_user.su_abs_id',
				'suivi'=>'select * from conges_periode',
				'tmpabs'=>'select * from conges_temp',
				'user_rtt'=>'select * from conges_artt',
				'type_abs'=>'select * from conges_type_absence',
				'ferie'=>'select * from conges_jours_feries',
				'del_conge'=>'select p_nb_jours, p_type, p_etat from conges_periode',
				'joff'=>'select p_num from conges_periode',
				'mail_headers'=>'select * from conges_mail',
				'etat_conge'=>'select p_etat from conges_periode',
				'headers' => 'select * from conges_mail'
				);
	}
?>