<?php
   $xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>";      
   //***********************************************************************************************************************
   // V1.05 : ChatBOT eedomus
   
	// recuperation des infos depuis la requete
    $value = getArg("value");
	$action = getArg("action");
	$notif = getArg("notif");
	$numchat = getArg("num");
	$periph_id = getArg('eedomus_controller_module_id'); 
	$api_output = getArg("output");
	$lang = getArg("lang");
	$isdebug = getArg("debug",$mandatory = false);
	
	// enregistrement auto du code api input par son capteur
	if ($action == "input") {
		$xml .= "<CHATBOT>".
		saveVariable("CHATBOT_INPUT_API_".$numchat, $periph_id);
		// récupère les paramètres des actions chatbot
		$param_action = array();
		$param_periph = array();
		$param_piece = array();
		$param_api = array();
		$param_value = array();
		$param_mdp = array();
		$preload = loadVariable("CHATBOT_API_".$numchat);
		if ($preload != '' && substr($preload, 0, 8) != "## ERROR") {
			$iparam = 0;
			
			$chatbot_api = $preload;
			$tab_param_list = getPeriphValueList($chatbot_api);
			foreach($tab_param_list As $tab_param_value) {
				if ($tab_param_value["value"] > 0) {
					$param_lu = explode(",",strtolower(sdk_noaccent($tab_param_value["state"])));
					$iparam++;
					$param_action[$iparam] = $param_lu[0];
					$param_periph[$iparam] = $param_lu[1];
					$param_piece[$iparam] = $param_lu[2];
					$param_api[$iparam] = $param_lu[3];
					if (count($param_lu) > 4) {
						$param_value[$iparam] = $param_lu[4];
					} else {
						$param_value[$iparam] = "";
					}
					if (count($param_lu) > 5) {
						$param_mdp[$iparam] = $param_lu[5];
					} else {
						$param_mdp[$iparam] = "";
					}
				}
			}
			saveVariable("CHATBOT_PARAM_ACTION_".$numchat, $param_action);
			saveVariable("CHATBOT_PARAM_PERIPH_".$numchat, $param_periph);
			saveVariable("CHATBOT_PARAM_PIECE_".$numchat, $param_piece);
			saveVariable("CHATBOT_PARAM_API_".$numchat, $param_api);
			saveVariable("CHATBOT_PARAM_VALUE_".$numchat, $param_value);
			saveVariable("CHATBOT_PARAM_MDP_".$numchat, $param_mdp);
			for($iparam = 1; $iparam <= count($param_action); $iparam++) {
				$xml .= "<PARAM_".$iparam.">".$param_action[$iparam].",".$param_periph[$iparam].",".$param_piece[$iparam].",".$param_api[$iparam].",".$param_value[$iparam].",".$param_mdp[$iparam]."</PARAM_".$iparam.">";
			}
			$xml .= "<MSG>CHATBOT ".$numchat."-".$chatbot_api."-".$iparam." params</MSG>";
			$xml .= "<INPUT>--</INPUT></CHATBOT>";
		} else {
			if (is_numeric($api_output) && $api_output > 1) {
				setValue($api_output, "(o_o)?");
			}
			$xml .= "<INPUT>!!(o_o)!!</INPUT></CHATBOT>";
		}
		
		sdk_header('text/xml');
		echo $xml;
	} // fin input
		
	if ($action == "request") {
		// enregistrement de l'API du chatBOT (Input y récupérera les paramètres)
		saveVariable("CHATBOT_API_".$numchat, $periph_id);
		// *****************************************************************************
		
		// DICTIONNAIRE CHATBOT ********************************************************
		$tab_nack_fr = array(0 => "Oups, je n'ai pas compris", 1 => "Désolé je ne comprends pas", 2 => "Pour moi, c'est du charabia...", 3 => "???!!!", 4 => "Il y a un problème", 5 => "KO");
		$tab_ack_fr  = array(0 => "Ok", 1 => "C'est parti", 2 => "Je fais ça", 3 => "Bien reçu", 4 => "L'action est en cours");
		$mdp_text_fr = "Mot de passe de confirmation ?";
		$att_api_text_fr = "Reessayer dans quelques minutes...";
		$bad_mdp_text_fr = "Erreur de mot de passe...désolé";
		$noapi_text_fr = "Pas de périphérique désigné (code API)";
		$att_param_text_fr = "Recommencer après intégration des paramètres...";
		$unit_temp_text_fr = " degrés";
		$unit_light_text_fr = "%";
		//-----------
		$tab_nack_en = array(0 => "Oops, i did not understand", 1 => "Sorry i don't get it", 2 => "What??", 3 => "???!!!", 4 => "Houston, we've got a problem", 5 => "KO");
		$tab_ack_en  = array(0 => "Ok", 1 => "Let's go", 2 => "Rock'n'roll", 3 => "Copy that", 4 => "Work in progress");
		$mdp_text_en = "Password ?";
		$att_api_text_en = "Please try again in a few minutes...";
		$att_param_text_en = "Please wait for parameters update...";
		$bad_mdp_text_en = "Wrong password";
		$noapi_text_en = "I need a device id";
		$unit_temp_text_en = " degrees";
		$unit_light_text_en = "%";
		//-----------
		$tab_nack_es = array(0 => "Oops, no entendí", 1 => "Lo siento, no entiendo", 2 => "hay un problema", 3 => "???!!!", 4 => "Que?", 5 => "KO");
		$tab_ack_es  = array(0 => "Ok", 1 => "Vamos", 2 => "bien recibido");
		$mdp_text_es = "Contraseña de confirmación ?";
		$att_api_text_es = "Vuelva a intentar en unos minutos...";
		$bad_mdp_text_es = "Contraseña incorrecta";
		$att_param_text_es = "Repita luego de la integración de los parametros...";
		$noapi_text_es = "Necesito el codigo API del dispositivo";
		$unit_temp_text_es = " grados";
		$unit_light_text_es = "%";
	
		// MOTS CLES
		$tab_desactiver = array("desactive", "etein", "stop", "coupe", "cut", "turn off", "switch off", "apaga", "desconect");
		$tab_activer = array("active", "allume", "actionne", "retabli", "illumin", "verrouill", "enclenche", "declenche", "turn on", "switch on", "start", "enciende", "encender");
		$tab_ouvrir = array("ouvre", "monte", "open", " up", "haut", "abrir", "abre");
		$tab_fermer = array("ferme", "descen", "baisse", "close", "down", "bas", "cierra", "cerrar");
		$tab_get = array("quel", "donne", "dit", "combien", "comment", "what", "est-il", "est-elle", "tell", "give", "how much", "get", "cual es", "dime", "cuanto", "que es");
		$tab_set = array("regle", "fixe", "defini", "met", "set ", "change", "positionne", "cojunt", "establec", "ajust", "pone ");
		$tab_lumiere = array("lumie", "lumin", "lustre", "lamp", "spot", "veilleuse", "light", "luz", "lampara");
		$tab_volet = array("volet", "VR", "shutter", "persiana");
		$tab_temperature = array("tempera", "thermostat", "termostat", "combien il fait", "combien fait-il", "degre");
		$tab_tv = array("tv", "televis", "la tele", "box", "chaine", "channel", "canal");
		$tab_radio = array("radio", "frequenc");
		$tab_ouvrant = array("porte", "portail", "portill", "fenetr", "velux", "puerta", "ventana", "portal");
		$tab_alarme = array("alarm", "intrusion");
		// Réponses interprétées
		$tab_reponses_lumiere_fr = array("start" => "J'allume ", "stop" => "J'éteins ", "open" => "J'allume ", "close" => "J'éteins ", "get" => "L'état de la lumière est ", "set" => "Je règle la luminosité à ");
		$tab_reponses_lumiere_en = array("start" => "I turn on the light ", "stop" => "I turn the light off ", "open" => "I turn on the light ", "close" => "I turn the light off ", "get" => "The light is ", "set" => "I adjust the brightness to ");
		$tab_reponses_lumiere_es = array("start" => "Enciendo", "stop" => "Apago", "open" => "Enciendo", "close" => "Apago", "get" => "El estado de la luz es ", "set" => "Ajusto el brillo a ");
		
		$tab_reponses_volet_fr = array("start" => "J'actionne le volet ", "stop" => "J'arrête le volet ", "open" => "J'ouvre ", "close" => "Je ferme ", "get" => "Le volet est ", "set" => "Je fixe le volet à ");
		$tab_reponses_volet_en = array("start" => "I operate the shutter ", "stop" => "I stop the shutter ", "open" => "I open the shutter ", "close" => "I close the shutter ", "get" => "The shutter is ", "set" => "I set the shutter to ");
		$tab_reponses_volet_es = array("start" => "Opero la persiana ", "stop" => "Detengo el obturador ", "open" => "Abro el obturador ", "close" => "Cierro el obturado ", "get" => "el obturador es ", "set" => "Posiciono el obturador ");
		
		$tab_reponses_ouvrant_fr = array("start" => "Je l'actionne ", "stop" => "Je l'arrête ", "open" => "J'ouvre ", "close" => "Je ferme ", "get" => "C'est ", "set" => "Je fixe à ");
		$tab_reponses_ouvrant_en = array("start" => "I operate it ", "stop" => "I stop it ", "open" => "I open it", "close" => "I close it ", "get" => "It's ", "set" => "I set it to ");
		$tab_reponses_ouvrant_es = array("start" => "Lo opero ", "stop" => "Lo detengo ", "open" => "Lo abro ", "close" => "Lo cierro ", "get" => "Esta ", "set" => "Lo rijo en ");
		
		$tab_reponses_temperature_fr = array("start" => "", "stop" => "", "open" => "", "close" => "", "get" => "Il fait ", "set" => "Je règle la température à ");
		$tab_reponses_temperature_en = array("start" => "", "stop" => "", "open" => "", "close" => "", "get" => "It is ", "set" => "I adjust the temperature to ");
		$tab_reponses_temperature_es = array("start" => "", "stop" => "", "open" => "", "close" => "", "get" => "Esta a ", "set" => "Ajusto la temperatura a ");
		
		$tab_reponses_alarme_fr = array("start" => "J'active l'alarme ", "stop" => "Je désactive l'alarme", "open" => "", "close" => "", "get" => "L'alarme est ", "set" => "");
		$tab_reponses_alarme_en = array("start" => "I activate the alarm ", "stop" => "I disable the alarm ", "open" => "", "close" => "", "get" => "Alarm is ", "set" => "");
		$tab_reponses_alarme_es = array("start" => "Activo la alarma ", "stop" => "Desactivo la alarma", "open" => "", "close" => "", "get" => "La alarma está ", "set" => "");
		
		$tab_reponses_tv_fr = array("start" => "J'allume la télévision ", "stop" => "J'éteins la télévision", "open" => "Je monte le volume ", "close" => "Je baisse le volume ", "get" => "TV - ", "set" => "");
		$tab_reponses_tv_en = array("start" => "I turn on the TV ", "stop" => "I turn off the TV ", "open" => "I turn up the volume ", "close" => "I lower the volume ", "get" => "TV - ", "set" => "");
		$tab_reponses_tv_es = array("start" => "Enciendo la television ", "stop" => "Apago la television ", "open" => "Subo el volumen ", "close" => "Baje el volumen ", "get" => "TV - ", "set" => "");
		
		$tab_reponses_radio_fr = array("start" => "J'allume la radio ", "stop" => "J'éteins la radio", "open" => "Je monte le volume ", "close" => "Je baisse le volume ", "get" => "Radio - ", "set" => "");
		$tab_reponses_radio_en = array("start" => "I turn on the radio ", "stop" => "I turn off the radio ", "open" => "I turn up the volume ", "close" => "I lower the volume ", "get" => "Radio - ", "set" => "");
		$tab_reponses_radio_es = array("start" => "Enciendo la radio ", "stop" => "Apago la radio ", "open" => "Subo el volumen ", "close" => "Baje el volumen ", "get" => "Radio - ", "set" => "");
		
		// **************************************
		// reglage de la langue
		$tab_nack = $tab_nack_fr;
		$tab_ack = $tab_ack_fr;
		$mdp_text = $mdp_text_fr;
		$att_api_text = $att_api_text_fr;
		$bad_mdp_text = $bad_mdp_text_fr;
		$noapi_text = $noapi_text_fr;
		$unit_temp_text = $unit_temp_text_fr;
		$unit_light_text = $unit_light_text_fr;
		$att_param_text = $att_param_text_fr;
		$tab_reponses_lumiere = $tab_reponses_lumiere_fr;
		$tab_reponses_volet = $tab_reponses_volet_fr;
		$tab_reponses_temperature = $tab_reponses_temperature_fr;
		$tab_reponses_ouvrant = $tab_reponses_ouvrant_fr;
		$tab_reponses_alarme = $tab_reponses_alarme_fr;
		$tab_reponses_tv = $tab_reponses_tv_fr;
		$tab_reponses_radio = $tab_reponses_radio_fr;
		if ($lang == "en") {
			$tab_nack = $tab_nack_en;
			$tab_ack = $tab_ack_en;
			$mdp_text = $mdp_text_en;
			$att_api_text = $att_api_text_en;
			$bad_mdp_text = $bad_mdp_text_en;
			$noapi_text = $noapi_text_en;
			$unit_temp_text = $unit_temp_text_en;
			$unit_light_text = $unit_light_text_en;
			$att_param_text = $att_param_text_en;
			$tab_reponses_lumiere = $tab_reponses_lumiere_en;
			$tab_reponses_volet = $tab_reponses_volet_en;
			$tab_reponses_temperature = $tab_reponses_temperature_en;
			$tab_reponses_ouvrant = $tab_reponses_ouvrant_en;
			$tab_reponses_alarme = $tab_reponses_alarme_en;
			$tab_reponses_tv = $tab_reponses_tv_en;
			$tab_reponses_radio = $tab_reponses_radio_en;
		}
		if ($lang == "es") {
			$tab_nack = $tab_nack_es;
			$tab_ack = $tab_ack_es;
			$mdp_text = $mdp_text_es;
			$att_api_text = $att_api_text_es;
			$bad_mdp_text = $bad_mdp_text_es;
			$noapi_text = $noapi_text_es;
			$unit_temp_text = $unit_temp_text_es;
			$unit_light_text = $unit_light_text_es;
			$att_param_text = $att_param_text_es;
			$tab_reponses_lumiere = $tab_reponses_lumiere_es;
			$tab_reponses_volet = $tab_reponses_volet_es;
			$tab_reponses_temperature = $tab_reponses_temperature_es;
			$tab_reponses_ouvrant = $tab_reponses_ouvrant_es;
			$tab_reponses_alarme = $tab_reponses_alarme_es;
			$tab_reponses_tv = $tab_reponses_tv_es;
			$tab_reponses_radio = $tab_reponses_radio_es;
		}
		
		// Vérifie que le plugin Notification est effectif pour répondre
		$notification = false;
		if (is_numeric($notif) && $notif > 1) {
			//$tab_notif = getPeriphValueList($notif);
			//foreach($tab_notif As $tab_notif_value) {
			//	if ($tab_notif_value["value"] == 9999) {
					$notification = true; 
			//		break;
			//	}
			//}
		}
		
		// chargement des paramètres
		$param_action = array();
		$preload = loadVariable("CHATBOT_PARAM_ACTION_".$numchat);
		if ($preload != '' && substr($preload, 0, 8) != "## ERROR") {
			$param_action = $preload;
		}
		$param_periph = loadVariable("CHATBOT_PARAM_PERIPH_".$numchat);
		$param_piece = loadVariable("CHATBOT_PARAM_PIECE_".$numchat);
		$param_api = loadVariable("CHATBOT_PARAM_API_".$numchat);
		$param_value = loadVariable("CHATBOT_PARAM_VALUE_".$numchat);
		$param_mdp = loadVariable("CHATBOT_PARAM_MDP_".$numchat);
		
		$input_ok = false;
		$api_input = "";
		// récupère l'api de l'INPUT
		$preload = loadVariable("CHATBOT_INPUT_API_".$numchat);
		if ($preload != '' && substr($preload, 0, 8) != "## ERROR") {
			$api_input = $preload;
			$tab_input = getValue($api_input, true);
			$input = strtolower(sdk_noaccent($tab_input['value']));
			if ($input != "" && $input != "--") {
				$input_ok = true;
				setValue($api_input, "--");
			}
		}
		$output_ok = false;
		// vérifie si on a l'api de l'OUTPUT en VAR2
		if (is_numeric($api_output) && $api_output > 1) {
			$output_ok = true;
			
		}
		// INPUT  OK
		if ($input_ok) {
			// obligé d'effacter les notifications en mémoire de tous les chatbots (car si même notificateur, il va lire d'anciens mesg d'autres BOT)
			// il faudrait une fonction qui permet au notificateur de supprimer la variable du chatbot une fois le msg traité.
			saveVariable("CHATBOT_9999_1", "");
			saveVariable("CHATBOT_9999_2", "");
			saveVariable("CHATBOT_9999_3", "");
			saveVariable("CHATBOT_9999_4", "");
			saveVariable("CHATBOT_9999_5", "");
			
		  $preload = loadVariable("CHATBOT_CHKPWD_".$numchat);
		  if ($preload != '' && substr($preload, 0, 8) != "## ERROR") {
		  	// on attend un mot de passe
			$password = $preload;
			if (strpos($input, $password) !== false) {
				// le mdp a été donné
				$apitoset = loadVariable("CHATBOT_CHKAPI_".$numchat);
				$valuetoset = loadVariable("CHATBOT_CHKVAL_".$numchat);
				$actiontoset = loadVariable("CHATBOT_CHKACT_".$numchat);
				$txt_reponse = loadVariable("CHATBOT_CHKREP_".$numchat);
				if ($actiontoset == "get") { // get
					// GET
					if (is_numeric($apitoset) && $apitoset > 1) {
						$request = getValue($apitoset, true);
						$request_text = $request['value_text'];
						$request_value = $request['value'];
						if ($request_text == "") {
							$request_text = $request_value;
						}
						
						$newnotif = sdk_notification($txt_reponse.$request_text);
					} else {
						if ($isdebug == 1) {
							$newnotif = sdk_notification($noapi_text."(".$debug.")");
						} else {
							$newnotif = sdk_notification($noapi_text);
						}
					}
				} else if ($actiontoset == "set") { // set
					if (is_numeric($apitoset) && $apitoset > 1) {
						// cherche la valeur donnée de l'input à positionner
						$pattern = '/.+[de|à|a|sur|to] +([0-9]+(\.|,|°|%)*[0-9]*) */';
						if (preg_match($pattern, $input, $matches) == 1) {
							$valuetoset = $matches[1];
							if ($valuetoset != "") {
								if ($txt_reponse != "") {
									$newnotif = sdk_notification($txt_reponse.$valuetoset);
								} else {
									$ack = sdk_ack("","");
								}
								setValue($apitoset, $valuetoset);
							} else {
								if ($isdebug == 1) {
									$nack = sdk_nack("",$debug."(".$input.")");
								} else {
									$nack = sdk_nack("","");
								}
							}
									
						} else {
							if ($isdebug == 1) {
								$nack = sdk_nack("",$debug."(".$input.")");
							} else {
								$nack = sdk_nack("","");
							}
						}
					} else {
						if ($isdebug == 1) {
							$newnotif = sdk_notification($noapi_text."(".$debug.")");
						} else {
							$newnotif = sdk_notification($noapi_text);
						}
					}
						
				} else {
					if (is_numeric($apitoset) && $apitoset > 1 && $valuetoset != "") {
						setValue($apitoset, $valuetoset);
						if ($txt_reponse != "") {
							$newnotif = sdk_notification($txt_reponse);
						} else {
							$ack = sdk_ack("","");
						}
					} else {
						if ($isdebug == 1) {
							$newnotif = sdk_notification($noapi_text."(".$debug.")");
						} else {
							$newnotif = sdk_notification($noapi_text);
						}
					}
					
				}
			} else {
				$newnotif = sdk_notification($bad_mdp_text);
			}
			saveVariable("CHATBOT_CHKPWD_".$numchat, "");
			saveVariable("CHATBOT_CHKAPI_".$numchat, "");
			saveVariable("CHATBOT_CHKVAL_".$numchat, "");
			saveVariable("CHATBOT_CHKACT_".$numchat, "");
			saveVariable("CHATBOT_CHKREP_".$numchat, "");
		  } else {
			// interpretation de l'input
			// recherche de l'action
			$actionlue = "";
			$periphlu = "";
			$piecelue = "";
			$desactiver = false;
			$activer = false;
			$get = false;
			$set = false;
			$ouvrir = false;
			$fermer = false;
			$lumiere = false;
			$volet = false;
			$temperature = false;
			$ouvrant = false;
			$alarme = false;
			$tv = false;
			$radio = false;
			
			
			// ACTION
			foreach($tab_get As $tab_get_value) {
				if (strpos($input, $tab_get_value) !== false) {
					$get = true;
					$actionlue = "get";
					break;
					}
			}
			if (!$get) {
				foreach($tab_desactiver As $tab_desactiver_value) {
					if (strpos($input, $tab_desactiver_value) !== false) {
						$desactiver = true;
						$actionlue = "stop";
						break;
					}
				}
			}
			if (!$get && !$desactiver) {
				foreach($tab_activer As $tab_activer_value) {
					if (strpos($input, $tab_activer_value) !== false) {
						$activer = true;
						$actionlue = "start";
						break;
					}
				}
			}
			
			if (!$get && !$desactiver && !$activer) {
				foreach($tab_ouvrir As $tab_ouvrir_value) {
					if (strpos($input, $tab_ouvrir_value) !== false) {
						$ouvrir = true;
						$actionlue = "open";
						break;
					}
				}
			}
			if (!$get && !$desactiver && !$activer && !$ouvrir) {
				foreach($tab_fermer As $tab_fermer_value) {
					if (strpos($input, $tab_fermer_value) !== false) {
						$fermer = true;
						$actionlue = "close";
						break;
					}
				}
			}
			if (!$get && !$desactiver && !$activer && !$ouvrir && !$fermer) {
				foreach($tab_set As $tab_set_value) {
					if (strpos($input, $tab_set_value) !== false) {
						$set = true;
						$actionlue = "set";
						break;
					}
				}
			}
			//*************************************************************************
			// PERIPHERIQUES
			foreach($tab_lumiere As $tab_lumiere_value) {
				if (strpos($input, $tab_lumiere_value) !== false) {
					$lumiere = true;
					$periphlu = "light|lumiere|luz";
					break;
				}
			}
			if (!$lumiere) {
				foreach($tab_volet As $tab_volet_value) {
					if (strpos($input, $tab_volet_value) !== false) {
						$volet = true;
						$periphlu = "shutter|volet|persiana";
						break;
					}
				}
			}
			
			if (!$lumiere && !$volet) {
				foreach($tab_temperature As $tab_temperature_value) {
					if (strpos($input, $tab_temperature_value) !== false) {
						$temperature = true;
						$periphlu = "temperature|temperatura";
						break;
					}
				}
			}
			
			if (!$lumiere && !$volet && !$temperature) {
				foreach($tab_ouvrant As $tab_ouvrant_value) {
					if (strpos($input, $tab_ouvrant_value) !== false) {
						$ouvrant = true;
						$periphlu = "porte|fenetre|door|window";
						break;
					}
				}
			}
			
			if (!$lumiere && !$volet && !$temperature && !$ouvrant) {
				foreach($tab_alarme As $tab_alarme_value) {
					if (strpos($input, $tab_alarme_value) !== false) {
						$alarme = true;
						$periphlu = "alarme";
						break;
					}
				}
			}
			
			if (!$lumiere && !$volet && !$temperature && !$ouvrant && !$alarme) {
				foreach($tab_tv As $tab_tv_value) {
					if (strpos($input, $tab_tv_value) !== false) {
						$tv = true;
						$periphlu = "tv|television";
						break;
					}
				}
			}
			if (!$lumiere && !$volet && !$temperature && !$ouvrant && !$alarme && !$tv) {
				foreach($tab_radio As $tab_radio_value) {
					if (strpos($input, $tab_radio_value) !== false) {
						$radio = true;
						$periphlu = "radio";
						break;
					}
				}
			}
			// Action trouvée, recherche paramètre correspondant
			$debug = "";
			if ($activer || $desactiver || $get || $ouvrir || $fermer || $set) {
			  $debug = "Action ".$actionlue;
			  if (count($param_action) > 0) {
				
				$needmdp = false;
				$understood = false;
				$needmdp_value = "";
				$apitoset = "";
				$valuetoset = "";
				$txt_reponse = "";
				for($iparam = 1; $iparam <= count($param_action); $iparam++) {
					if ($param_action[$iparam] == $actionlue) {
						$debug .= " Act";
						// Le paramètre correspond à l'action demandée
						if (strpos($periphlu, $param_periph[$iparam]) !== false) {
							$debug .= " Periph";
							if ($periphlu == "light|lumiere|luz") {
								$txt_reponse = $tab_reponses_lumiere[$actionlue];
							}
							if ($periphlu == "shutter|volet|persiana") {
								$txt_reponse = $tab_reponses_volet[$actionlue];
							}
							if ($periphlu == "porte|fenetre|door|window") {
								$txt_reponse = $tab_reponses_ouvrant[$actionlue];
							}
							if ($periphlu == "temperature|temperatura") {
								$txt_reponse = $tab_reponses_temperature[$actionlue];
							}
							if ($periphlu == "alarme") {
								$txt_reponse = $tab_reponses_alarme[$actionlue];
							}
							if ($periphlu == "tv|television") {
								$txt_reponse = $tab_reponses_tv[$actionlue];
							}
							if ($periphlu == "radio") {
								$txt_reponse = $tab_reponses_radio[$actionlue];
							}
							// Le paramètre correspond au périphérique demandé
							if (strpos($input, $param_piece[$iparam]) !== false || $param_piece[$iparam] == "") {
								$debug .= " Piece";
								// Le paramètre correspond à la pièce demandée
								$understood = true;
								$apitoset = $param_api[$iparam];
								$valuetoset = $param_value[$iparam];
								if ($param_mdp[$iparam] != "") {
									// demande de confirmation par mdp
									$needmdp = true;
									$needmdp_value = $param_mdp[$iparam];
									$debug .= " Mdp";
								}
								break;
							}
						}
					}
				}
				if ($needmdp) {
					// demande password
					saveVariable("CHATBOT_CHKPWD_".$numchat, $needmdp_value);
					saveVariable("CHATBOT_CHKAPI_".$numchat, $apitoset);
					saveVariable("CHATBOT_CHKVAL_".$numchat, $valuetoset);
					saveVariable("CHATBOT_CHKACT_".$numchat, $actionlue);
					saveVariable("CHATBOT_CHKREP_".$numchat, $txt_reponse);
					$newnotif = sdk_notification($mdp_text);
				} else { // il n'y a pas de demande de mot de passe
				
					if ($understood) {
						if ($get) { // requête
							$debug .= " Get";
							if (is_numeric($apitoset) && $apitoset > 1) {
								$request = getValue($apitoset, true);
								$request_text = $request['value_text'];
								$request_value = $request['value'];
								if ($request_text == "") {
									$request_text = $request_value;
								}
								// mettre l'unité en fonction de la requete periph..
								if ($temperature && strpos($request_text, "°") == false) {
									$request_text .= $unit_temp_text;
								}
								if (($lumiere || $volet) && strpos($request_text, "%") == false) {
									$request_text .= $unit_light_text;
								}
								
								//
								$newnotif = sdk_notification($txt_reponse.$request_text);
							} else {
								if ($isdebug == 1) {
									$newnotif = sdk_notification($noapi_text."(".$debug.")");
								} else {
									$newnotif = sdk_notification($noapi_text);
								}
							}
						} else if ($set) { // set
							$debug .= " ".$apitoset;
							if (is_numeric($apitoset) && $apitoset > 1) {
								// cherche la valeur donnée de l'input à positionner
								$pattern = '/.+(de|à|a|sur|to|en|channel|chaine|canal|radio) +([0-9]+(\.|,|°|%)*[0-9]*) */'; // valeur numérique après le mot "de, à, a, sur, to, en"
								$pattern2 = '/.+(de|à|a|sur|to|en|channel|chaine|canal|radio) +([a-zA-Z]+)/'; // valeur non numérique en fin de phrase après le mot "de, à, a, sur, to, en"
								if (preg_match($pattern, $input, $matches) == 1) { 
									// valeur numérique
									$valuetoset = $matches[2];
									$debug .= " REGEX1 ".$matches[2];
									if ($valuetoset != "") {
										if ($txt_reponse != "") {
											$newnotif = sdk_notification($txt_reponse.$valuetoset);
										} else {
											$ack = sdk_ack("","");
										}
										
										setValue($apitoset, $valuetoset);
									} else {
										if ($isdebug == 1) {
											$nack = sdk_nack("",$debug."(".$input.")");
										} else {
											$nack = sdk_nack("","");
										}
									}
								} else if (preg_match($pattern2, $input, $matches) == 1) {
									// valeur non numérique
									$valuetoset = $matches[2];
									$debug .= " REGEX2 ".$matches[2];
									if ($valuetoset != "") {
										if ($txt_reponse != "") {
											$newnotif = sdk_notification($txt_reponse.$valuetoset);
										} else {
											$ack = sdk_ack("","");
										}
										setValue($apitoset, $valuetoset);
									} else {
										if ($isdebug == 1) {
											$nack = sdk_nack("",$debug."(".$input.")");
										} else {
											$nack = sdk_nack("","");
										}
									}
								} else {
									if ($isdebug == 1) {
											$nack = sdk_nack("",$debug."(".$input.")");
									} else {
											$nack = sdk_nack("","");
									}
								}
								
								
							
							} else {
								if ($isdebug == 1) {
									$newnotif = sdk_notification($noapi_text."(".$debug.")");
								} else {
									$newnotif = sdk_notification($noapi_text);
								}
							}
						
						} else { // action
							if (is_numeric($apitoset) && $apitoset > 1 && $valuetoset != "") {
								setValue($apitoset, $valuetoset);
								if ($txt_reponse != "") {
									$ack = sdk_notification($txt_reponse);
								} else {
									$ack = sdk_ack("","");
								}
							} else {
								if ($isdebug == 1) {
									$newnotif = sdk_notification($noapi_text."(".$debug.")");
								} else {
									$newnotif = sdk_notification($noapi_text);
								}
							}
						}
					} else { // je n'ai pas compris
						if ($isdebug == 1) {
							$nack = sdk_nack("",$debug."(".$input.")");
						} else {
							$nack = sdk_nack("","");
						}
					}
				} // fin test mdp demandé
			  } else { // fin pas de param_action
			      $newnotif = sdk_notification($att_param_text);
			  }
			} else if ($input == "!!(o_o)!!" || $input == "init") { 
				  $newnotif = sdk_notification("RocknRoll");
			
			} else {	// action non reconnue parmi $activer || $desactiver || $get || $ouvrir || $fermer || $set
				if ($isdebug == 1) {
					$nack = sdk_nack("",$debug."(".$input.")");
				} else {
					$nack = sdk_nack("","");
				}
				
			}
		  } // fin test si attente mdp
		} else { // input ko
			$newnotif = sdk_notification($att_api_text);
		} // fin input 
	} // fin request
	
	
	// Retourner une incompréhension
	function sdk_nack($prefixe, $val) {
		global $tab_nack;
		global $notif;
		global $api_output;
		global $notification;
		global $output_ok;
		global $numchat;
		
		$randomidx = rand(0, count($tab_nack) - 1);
		if ($output_ok) {
			if ($val != "") {
				setValue($api_output, $prefixe.sdk_noaccent($tab_nack[$randomidx])."-".$val);
			} else {
				setValue($api_output, $prefixe.sdk_noaccent($tab_nack[$randomidx]));
			}
		}
		if ($notification) {
			saveVariable("CHATBOT_9999_".$numchat, $prefixe.$tab_nack[$randomidx]);
			setValue($notif, 9999);
		}
	}
	
	// Retourner une validation
	function sdk_ack($prefixe, $val) {
		global $tab_ack;
		global $notif;
		global $api_output;
		global $notification;
		global $output_ok;
		global $numchat;
		$randomidx = rand(0, count($tab_ack) - 1);
		if ($output_ok) {
			if ($val != "") {
				setValue($api_output, $prefixe.sdk_noaccent($tab_ack[$randomidx])."-".$val);
			} else {
				setValue($api_output, $prefixe.sdk_noaccent($tab_ack[$randomidx]));
			}
			
		}
		if ($notification) {
			saveVariable("CHATBOT_9999_".$numchat, $prefixe.sdk_noaccent($tab_ack[$randomidx]));
			setValue($notif, 9999);
		}
	}
	
	// Notification
	function sdk_notification($text) {
		global $notification;
		global $notif;
		global $api_output;
		global $output_ok;
		global $numchat;
		if ($output_ok) {
			setValue($api_output, $text);
		}
		if ($notification) {
			saveVariable("CHATBOT_9999_".$numchat, $text);
			setValue($notif, 9999);
		}
	}
	
	function sdk_noaccent($text) {
		$utf8_keys = array(	'/[áâãäà]/','/[ÁÀÂÃÄ]/', '/[ÍÌÎÏ]/', '/[íìîï]/', '/[éèêë]/', '/[ÉÈÊË]/', '/[óòôõö]/', '/[ÓÒÔÕÖ]/', '/[úûùü]/', '/[ÚÙÛÜ]/' , '/ç/', '/ñ/', '/Ñ/');
		$utf8_values = array('a', 'A', 'I',	'i', 'e', 'E', 'o',	'O', 'u', 'U', 'c', 'n', 'N');
	return preg_replace($utf8_keys, $utf8_values, $text);
	}	
?>
