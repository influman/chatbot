# Installation
Interagissez avec eedomus, � la mani�re d'un chatBOT 
    
### Pr�requis Notification
Pour recevoir les r�ponses du chatBOT eedomus, il vous faut installer au pr�alable un des plugins de notifications compatible :  
* Notifications IFTTT (Telegram, Twitter, Notifications)  
* Notifications FreeSMS Mobile  
* Notifications TTS Imperihome
...
  
Se r�f�rer � la documentation du plugin pour l'installation.  
Ces plugins de Notifications incluent une valeur [CHATBOT] pour assurer la compatibilit� avec ce plugin chatBOT.  
  

### Ajout du p�riph�rique et installation
Cliquez sur "Configuration" / "Ajouter ou supprimer un p�riph�rique" / "Store eedomus" / "chatBOT" / "Cr�er"  
    
*Voici les diff�rents champs � renseigner:*

* [Obligatoire] - Le num�ro de chatBOT (1 par d�faut, l'incr�menter � chaque ajout de plugin)
* [Obligatoire] - La langue de discussion : fran�ais, anglais, espagnol
* [Obligatoire] - Le plugin Notifications � s�lectionner (Notifications Telegram)
  
  
![STEP0](https://i.imgur.com/IOThdvB.png) 
  
  
### Suivre cette proc�dure post-installation
Trois p�riph�riques et une r�gle sont install�s :
  
* chatBOT - Param�tres : pour param�trer l'interpr�tation
* chatBOT - Input : l� o� la question doit �tre d�pos�e
* chatBOT - Output : l� o� la r�ponse peut �tre d�pos�e par le BOT (facultatif) en compl�ment de la notification.
* 1 R�gle automatique pour lancer le chatBOT � la d�tection d'une nouvelle question post�e dans l'Input.
  
![STEP1](https://i.imgur.com/As0VVtN.png) 
  
Les trois actions suivantes sont facultatives si vous avez une version 1.1 au minimum des notificateurs et si vous ne souhaitez pas que la r�ponse soit enregistr�e dans Output.  
Elles sont donc obligatoires sinon.  
    
* Ouvrez la configuration du p�riph�rique "chatBOT - Output" et notez son code API. Annuler pour sortir.  
* Ouvrez la configuration du p�riph�rique de notification (ex. Notifications Telegram) et notez le code API "Output" dans [VAR3]. Sauvegardez.  
* Ouvrez la configuration du p�riph�rique "chatBOT - Input" et copiez le code API de l'output dans la zone [VAR2]. Sauvegardez.  
  
  
Notez  tout de m�me le code API du p�riph�rique "Input" pour la suite.
  
    
### Alimentation de "Input" par IFTTT (Telegram)
Les questions doivent �tre d�pos�es dans le p�riph�rique "Input" par le moyen de votre choix (R�gle, script, etc..).  
  
Pour poser des questions via Telegram, cr�er une Applet IFTTT (d�tail dans la documentation du plugin ASK) : 
  
* THIS : s�lectionner Telegram et "New message with key phrase", s�lectionner un hashtag d�clencheur (exemple : #ee)
* THAT : s�lectionner Webhooks et "Make a web request", dans le champ URL, ins�rez l'appel API eedomus suivant (avec vos codes personnels):

<https://api.eedomus.com/set?action=periph.value&periph_id=123456&value={{Text}}&api_user=XXXX&api_secret=aaaaaaaaaaaaaa>  
o� 123456 est le code API du p�riph�rique "Input" du chatBOT, et {{Text}} l'ingr�dient IFTTT du message Telegram.  
  
![STEP2](https://i.imgur.com/3Z2yRzA.png)
  
  
### Alimentation de "Input" par Google Home/IFTTT
Pour poser des questions via Google Home, cr�er une Applet IFTTT : 
  
* THIS : s�lectionner Google Assistant et "Say a phrase with a text ingredient"...
* ...s�lectionner 1 � 3 mots cl�s d�clencheurs de votre choix suivi d'un $ (exemple : eedomus $)...
* ...choisir un message de r�ponse instantan� de GH (exemple : Ok, je demande), et le langage choisi ("French")
* THAT : s�lectionner Webhooks et "Make a web request", dans le champ URL, ins�rez l'appel API eedomus suivant (avec vos codes personnels):

<https://api.eedomus.com/set?action=periph.value&periph_id=123456&value={{Text}}&api_user=XXXX&api_secret=aaaaaaaaaaaaaa>   
o� 123456 est le code API du p�riph�rique "Input" du chatBOT, et {{TextField}} l'ingr�dient IFTTT du message Google Assistant.  
  
![STEP10](https://i.imgur.com/s6CV0yR.png)
  
  
### Utilisation et param�trage
Avec le plugin Notification IFTTT (Telegram), et un "Input" aliment� par Telegram, vous pouvez alors int�ragir avec eedomus via l'application de chat Telegram.  
Vous poserez alors votre question via Telegram et le hashtag #ee, exemple :
  
  * #ee active l'alarme
  * #ee ouvre le volet du salon
  * #ee quelle est la temp�rature ext�rieure ?
  * #ee R�gle le thermostat de salon sur 22 degr�s
  
Pour que le chatBOT fasse le lien entre votre question et vos p�riph�riques r�els, vous devez param�trer les diff�rentes valeurs du p�riph�rique "chatBOT - Param�tres".  
Ouvrez la configuration du p�riph�rique et allez sur l'onglet Valeurs, puis affichez les valeurs masqu�es.  
Le plugin est install� avec des exemples de valeurs pr�d�finies.  
  
Une interpretation doit �tre param�tr�e dans l'ordre suivant, chaque crit�re s�par� d'une virgule, parmi la liste de valeurs suivantes :  

  * L'action : Start ou Stop ou Open ou Close ou Cast ou Launch ou Get ou Set
  * Le type de p�riph�rique : Lumi�re ou Volet ou Porte ou Temperature ou Alarme ou Television ou Radio ou Ambiance ou Camera
  * Un mot cl� discriminant : par exemple la pi�ce (non obligatoire)
  * Le code API du p�riph�rique � actionner/interroger
  * La valeur � positionner (si p�riph�rique actionn�, vide si GET ou SET)
  * Le mot de passe : s'il faut une confirmation pour obtenir la r�ponse du chatBOT (non obligatoire)
  
Exemples (avec question Telegram) :  
  
  * Start,Lumi�re,Salon,123456,100 : Positionnera le p�riph�rique 123456 � 100 � la demande "#ee allume la lampe du salon" par exemple  
  * Start,Television,,334455,1 : Positionne le p�riph�rique 334455 � 1 � la demande "#ee allume la t�l�" par exemple  
  * Start,Camera,Jardin,777777,1 : Positionne le p�riph�rique 777777 � 1 � la demande "#ee prend une photo du jardin" par exemple  
  * Stop,Lumi�re,Salon,123456,0 : Positionnera le p�riph�rique 123456 � 0 � la demande "#ee �teind la lumi�re du salon" par exemple  
  * Open,Volet,Salon,888888,2 : P�riph�rique 888888 � 2 � la question "#ee Ouvre le volet du salon"  
  * Close,Television,,334455,10 : P�riph�rique 334455 � 10 � la question "#ee baisse la tv"  par exemple  
  * Get,Porte,Garage,123456 : Retourne l'�tat de la porte de garage (ouverte ou ferm�e) � la question "#ee quel est l'�tat de la porte du garage ?" par exemple  
  * Get,Alarme,,22222,,password : Retourne l'�tat de l'alarme apr�s avoir donn� le mot de passe "password" � la question "#ee quel est le statut de l'alarme?" par exemple  
  * Set,Temp�rature,Salon,123456 : Fixe la temp�rature du salon (p�rip 123456) � la valeur num�rique transmise dans la demande  
  * Set,Television,,334455 : R�gle la t�l�vision sur la 23, � la question "#ee mets la cha�ne 23"  
  * Set,Ambiance,Salon,555555 : R�gle l'ambiance � "Lumineux", � la question "#ee Peux-tu mettre l'ambiance Lumineux dans le salon"
  * Launch,Simulation,,987654,100 : P�riph�rique 987654 � 100 � la demande "#ee Lance la simulation de pr�sence"  
  
  
Vous pouvez simplement envoyer un texte en output ou notification avec une commande de diffusion :  
  * #ee diffuse ceci est un test de diffusion
  * #ee dit il fait beau et chaud
  
Cela permet par exemple d'envoyer le texte dans l'output via un chatbot d�di� pour qu'il soit ensuite lu en notification dans la maison  
  
  
NB1 : si mot de passe demand�, via Telegram, il faut le saisir avec le hashtag d�clencheur du chatbot "#ee"  
NB2 : si mot de passe demand�, via Google Home, il faut le donner apr�s "Ok Google eedomus", dans le cas o� "eedomus" est votre mot-cl� d�clencheur du chatbot. 

![STEP3](https://i.imgur.com/9zI2zE2.png)
  
  
### Multi-usages

Si vous souhaitez pouvoir interagir par tchat via Telegram, dans les deux sens de communication,  
avec, par ailleurs, la possibilit� d'�changer via Google Home avec cette fois-ci r�ponse transmise sur TTS Imperihome,   
alors il vous faut installer deux plugins chatbot :  
  
  * Le premier chatBot est � installer avec Notifications Telegram (et un applet IFTTT pour poster les demandes Telegram dans l'input). Et ses propres param�tres.
  * Le second chatBOT est � installer avec Notifications API TTS (et un applet IFTTT pour poster les demandes Google Assistant dans l'input). Avec �galement ses propres param�tres.
  
N'oubliez pas d'incr�menter le num�ro de chatBOT � l'installation. 
  
Si vous ne voulez pas de notification apr�s avoir choisi GH comme input par exemple, il vous suffit de supprimer le contenu de [VAR1] du p�riph�rique chatBOT - Param�tres.  
Ainsi le chatBOT n'enverra pas le contenu d'output en notification.  

### Initialisation
  
La premi�re �tape consiste � attendre que la valeur "--" soit automatiquement positionn�e sur le capteur Input.  
Le chatBOT est cens� alors s'initialiser automatiquement apr�s quelques minutes.  
    
En cas de mise � jour ou r�installation du plugin, il faudra une premi�re ex�cution d'une question pour l'initialiser.  
Donnez dans ce cas l'input "init" par exemple.  

En cas de mise � jour des param�tres d'interpretation ou de langage, il faut un polling du capteur "Input" pour que les mises � jour soient prises en compte (30mn).  
  
  
Influman 2018-2019  
therealinfluman@gmail.com  
[Paypal Me](https://www.paypal.me/influman "paypal.me")  
  



 



