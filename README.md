# Fishikawa Project
The **Fishikawa Project** and the Fishikawa - **Root Cause Analysis** (FRCA) script are a community based, open Source project aimed at reducing the time it takes to diagnose and resolve common issues or problems that may be observed when installing, running or managing the Joomla!® Open Source CMS.

**Root Cause Analysis**  
The FRCA script is a single, downloadable, php script file, when opened in a browser on your website will attempt to diagnose and visually report any discvoered common or well known server, php and Joomla! misconfigurations or issues that may cause problems for the site administrators and/or end-users.

The FRCA script also includes reporting of any known **vulnerable extensions** based on the Joomla! projects "_Vulnerable Extension List (VEL)_".

Reported problems are subsequently categorised in to 4 _**impact**_ groups;  
- **Critical**
- **Moderate**
- **Minor**
- **Best Practice**  

Each individual problem report further describes the _**importance**_ or _**risk**_ atributed to the problem resolution;  
- **1** - Fatal/ShowStopper/High  
- **2** - Partially Unuseable/Medium  
- **3** - Non-Fatal/Recoverable/Low
- **4** - Reference/Notice/Unclassifed

**Pre~Installation Environment Check**  
FRCA may also be used even if Joomla! is not installed, acting as a "_Pre~Installation_" server and php configuration check, giving the user some insight or confidence that Joomla! will be able to be installed or run without issues.
  
  
---
  
  
#### Supported Environments & Resources
  
| Resource        |        |           |            |          |
|:----------------|:------:|:---------:|:----------:|:--------:|
| **Joomla\!**    | 2.5.x  |   3.x.x   |  4.x.x     |    \-    |
| **PHP**         | 5.4.x  |   7.x.x   |  8.x.x     |    \-    |
| **Databases**   | MySQL  |  MariaDB  | PostgreSQL |    \-    |
| **Web Servers** | Apache | LiteSpeed |  nginx     |   IIS    |
  
_**Note:** The Fishikawa Project & FRCA script **do not support** Joomla! 1.0.x, 1.5.x, 1.6.x or 1.7.x or PHP 5.3.x or below but the script may still work, be aware though your mileage and accuracy of results may vary. Any support requests for these environments will go unanswered and be closed immediately._
  
  
#### End User Support & Help
For FRCA support or help, please visit the [FRCA documentation site](https://fishikawarca.github.io/frca/docs/) first, if your problem persists [open an issue](https://github.com/FishikawaRCA/frca/issues/new?labels=user-support) describing your problem in detail and what you have tried to overcome it. 
  
  
---
  
  
### Security Policy
The FRCA is a readonly script, as such it should not have any security implications or introduce any security issues itself. If you believe you have found a security issue, please report it at the [FRCA Issues page](https://github.com/FishikawaRCA/frca/issues/new?labels=security) and someone will contact you in due course. 

> _Please **do not** include complete details or replication instructions in your report, just a brief description will suffice at that time._
  
  
---  
  
  
## Contributing - _Get Involved..._

### Language Translations
If you would like to contribute or help with translating the FRCA, please visit the [FRCA-Translations repository](https://github.com/FishikawaRCA/frca-translations) for more information.

> **English Localisation**:  
> _The FRCA default language is **English** (en-GB), to avoid unnecessary overhead and resource use, **all "en" languages** are treated as "**en-GB**" with no other "en" localisations planned._

> **RTL User Note:**  
> _Whilst the FRCA does support RTL languages, it currently _**does not**_ support RTL page display._

### Reviewers & Testers
The FRCA and the troubleshooting assistance it provides takes quite a bit of planning and logic and we don't always get everything right. 

If you would like to be part of the FRCA _Quality Assurance process_ and get involved in reviewing and testing bug-fixes, enhancements and troubleshooting procedures, _**[download the current version](https://github.com/FishikawaRCA/frca)**_, _**test it**_ and _**report any issues**_ you may find _**or suggestions**_ you might have...  
  
  
## Other Fishikawa Project Initiatives
The FRCA makes exensive use of the Fishikawa "_**Problem Determination Aide**_" _(PDA)_ json database, to find out more about this project, please visit the [Fishikawa PDA repository](https://github.com/FishikawaRCA/pda).  
  
  
---
  
  
##### Licensing & Notices
> **The FRCA comes with ABSOLUTELY NO WARRANTY.** _This is free software, and covered under the GNU GPLv3 or later license. You are welcome to redistribute it under certain conditions._
>
> _For details read the LICENSE file included in the download package with this script. A copy of the license may also be obtained at https://www.gnu.org/licenses/_

> _**The Fishikawa Project & FRCA script** are not affiliated with or endorsed by The Joomla! Project™. Use of the Joomla!® name, symbol, logo, and related trademarks is licensed by Open Source Matters, Inc._
