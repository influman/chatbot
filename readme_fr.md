# Installation
Interagissez avec eedomus, à la manière d'un chatBOT 
    
### Prérequis Notification
Pour recevoir les réponses du chatBOT eedomus, il vous faut installer au préalable un des plugins de notifications compatible :  
* Notifications IFTTT (Telegram, Twitter, Notifications)  
* Notifications FreeSMS Mobile  
* Notifications TTS Imperihome
...
  
Se référer à la documentation du plugin pour l'installation.  
Ces plugins de Notifications incluent une valeur [CHATBOT] pour assurer la compatibilité avec ce plugin chatBOT.  
  

### Ajout du périphérique et installation
Cliquez sur "Configuration" / "Ajouter ou supprimer un périphérique" / "Store eedomus" / "chatBOT" / "Créer"  
    
*Voici les différents champs à renseigner:*

* [Obligatoire] - Le numéro de chatBOT (1 par défaut, l'incrémenter à chaque ajout de plugin)
* [Obligatoire] - La langue de discussion : français, anglais, espagnol
* [Obligatoire] - Le plugin Notifications à sélectionner (Notifications Telegram)
  
  
![STEP0](https://i.imgur.com/IOThdvB.png) 
  
  
### Suivre cette procédure post-installation
Trois périphériques et une règle sont installés :
  
* chatBOT - Paramètres : pour paramétrer l'interprétation
* chatBOT - Input : là où la question doit être déposée
* chatBOT - Output : là où la réponse peut être déposée par le BOT (facultatif) en complément de la notification.
* 1 Règle automatique pour lancer le chatBOT à la détection d'une nouvelle question postée dans l'Input.
  
![STEP1](https://i.imgur.com/As0VVtN.png) 
  
Les trois actions suivantes sont facultatives si vous avez une version 1.1 au minimum des notificateurs et si vous ne souhaitez pas que la réponse soit enregistrée dans Output.  
Elles sont donc obligatoires sinon.  
    
* Ouvrez la configuration du périphérique "chatBOT - Output" et notez son code API. Annuler pour sortir.  
* Ouvrez la configuration du périphérique de notification (ex. Notifications Telegram) et notez le code API "Output" dans [VAR3]. Sauvegardez.  
* Ouvrez la configuration du périphérique "chatBOT - Input" et copiez le code API de l'output dans la zone [VAR2]. Sauvegardez.  
  
  
Notez  tout de même le code API du périphérique "Input" pour la suite.
  
    
### Alimentation de "Input" par IFTTT (Telegram)
Les questions doivent être déposées dans le périphérique "Input" par le moyen de votre choix (Règle, script, etc..).  
  
Pour poser des questions via Telegram, créer une Applet IFTTT (détail dans la documentation du plugin ASK) : 
  
* THIS : sélectionner Telegram et "New message with key phrase", sélectionner un hashtag déclencheur (exemple : #ee)
* THAT : sélectionner Webhooks et "Make a web request", dans le champ URL, insérez l'appel API eedomus suivant (avec vos codes personnels):

https://api.eedomus.com/set?action=periph.value&periph_id=123456&value={{Text}}&api_user=XXXX&api_secret=aaaaaaaaaaaaaa  
où 123456 est le code API du périphérique "Input" du chatBOT, et {{Text}} l'ingrédient IFTTT du message Telegram.  
  
![STEP2](https://i.imgur.com/3Z2yRzA.png)
  
  
### Alimentation de "Input" par Google Home/IFTTT
Pour poser des questions via Google Home, créer une Applet IFTTT : 
  
* THIS : sélectionner Google Assistant et "Say a phrase with a text ingredient"...
* ...sélectionner 1 à 3 mots clés déclencheurs de votre choix suivi d'un $ (exemple : eedomus $)...
* ...choisir un message de réponse instantané de GH (exemple : Ok, je demande), et le langage choisi ("French")
* THAT : sélectionner Webhooks et "Make a web request", dans le champ URL, insérez l'appel API eedomus suivant (avec vos codes personnels):

https://api.eedomus.com/set?action=periph.value&periph_id=123456&value={{Text}}&api_user=XXXX&api_secret=aaaaaaaaaaaaaa  
où 123456 est le code API du périphérique "Input" du chatBOT, et {{TextField}} l'ingrédient IFTTT du message Google Assistant.  
  
![STEP10](https://i.imgur.com/s6CV0yR.png)
  
  
### Utilisation et paramétrage
Avec le plugin Notification IFTTT (Telegram), et un "Input" alimenté par Telegram, vous pouvez alors intéragir avec eedomus via l'application de chat Telegram.  
Vous poserez alors votre question via Telegram et le hashtag #ee, exemple :
  
  * #ee active l'alarme
  * #ee ouvre le volet du salon
  * #ee quelle est la température extérieure ?
  * #ee Règle le thermostat de salon sur 22 degrés
  
Pour que le chatBOT fasse le lien entre votre question et vos périphériques réels, vous devez paramétrer les différentes valeurs du périphérique "chatBOT - Paramètres".  
Ouvrez la configuration du périphérique et allez sur l'onglet Valeurs, puis affichez les valeurs masquées.  
Le plugin est installé avec des exemples de valeurs prédéfinies.  
  
Une interpretation doit être paramétrée dans l'ordre suivant, chaque critère séparé d'une virgule, parmi la liste de valeurs suivantes :  

  * L'action : Start ou Stop ou Open ou Close ou Get ou Set
  * Le type de périphérique : Lumière ou Volet ou Porte ou Température ou Alarme
  * Un mot clé discriminant : par exemple la pièce (non obligatoire)
  * Le code API du périphérique à actionner/interroger
  * La valeur à positionner (si périphérique actionné, vide si GET ou SET)
  * Le mot de passe : s'il faut une confirmation pour obtenir la réponse du chatBOT (non obligatoire)
  
Exemples (avec question Telegram) :  
  
  * Start,Lumière,Salon,123456,100 : Positionnera le périphérique 123456 à 100 à la demande "#ee allume la lampe du salon" par exemple  
  * Stop,Lumière,Salon,123456,0 : Positionnera le périphérique 123456 à 0 à la demande "#ee éteind la lumière du salon" par exemple  
  * Open,Volet,Salon,888888,2 : Périphérique 888888 à 2 à la question "#ee Ouvre le volet du salon"  
  * Get,Porte,Garage,123456 : Retourne l'état de la porte de garage (ouverte ou fermée) à la question "#ee quel est l'état de la porte du garage ?" par exemple  
  * Get,Alarme,,22222,,password : Retourne l'état de l'alarme après avoir donné le mot de passe "password" à la question "#ee quel est le statut de l'alarme?" par exemple  
  * Set,Température,Salon,123456 : Fixe la température du salon (périp 123456) à la valeur numérique transmise dans la demande  
  
NB1 : si mot de passe demandé, via Telegram, il faut le saisir avec le hashtag déclencheur du chatbot "#ee"  
NB2 : si mot de passe demandé, via Google Home, il faut le donner après "Ok Google eedomus", dans le cas où "eedomus" est votre mot-clé déclencheur du chatbot. 

![STEP3](https://i.imgur.com/9zI2zE2.png)
  
  
### Multi-usages

Si vous souhaitez pouvoir interagir par tchat via Telegram, dans les deux sens de communication,  
avec, par ailleurs, la possibilité d'échanger via Google Home avec cette fois-ci réponse transmise sur TTS Imperihome,   
alors il vous faut installer deux plugins chatbot :  
  
  * Le premier chatBot est à installer avec Notifications Telegram (et un applet IFTTT pour poster les demandes Telegram dans l'input). Et ses propres paramètres.
  * Le second chatBOT est à installer avec Notifications API TTS (et un applet IFTTT pour poster les demandes Google Assistant dans l'input). Avec également ses propres paramètres.
  
N'oubliez pas d'incrémenter le numéro de chatBOT à l'installation. 
  
Si vous ne voulez pas de notification après avoir choisi GH comme input par exemple, il vous suffit de supprimer le contenu de [VAR1] du périphérique chatBOT - Paramètres.  
Ainsi le chatBOT n'enverra pas le contenu d'output en notification.  

### Initialisation
  
La première étape consiste à attendre que la valeur "--" soit automatiquement positionnée sur le capteur Input.  
Le chatBOT est censé alors s'initialiser automatiquement après quelques minutes.  
    
En cas de mise à jour ou réinstallation du plugin, il faudra une première exécution d'une question pour l'initialiser.  
Donnez dans ce cas l'input "init" par exemple.  



 



