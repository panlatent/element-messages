Element Messages
================
[![Build Status](https://travis-ci.org/panlatent/element-messages.svg)](https://travis-ci.org/panlatent/element-messages)
[![Coverage Status](https://coveralls.io/repos/github/panlatent/element-messages/badge.svg?branch=master)](https://coveralls.io/github/panlatent/element-messages?branch=master)
[![Latest Stable Version](https://poser.pugx.org/panlatent/element-messages/v/stable.svg)](https://packagist.org/packages/panlatent/element-messages)
[![Total Downloads](https://poser.pugx.org/panlatent/element-messages/downloads.svg)](https://packagist.org/packages/panlatent/element-messages) 
[![Latest Unstable Version](https://poser.pugx.org/panlatent/element-messages/v/unstable.svg)](https://packagist.org/packages/panlatent/element-messages)
[![License](https://poser.pugx.org/panlatent/element-messages/license.svg)](https://packagist.org/packages/panlatent/element-messages)
[![Craft CMS](https://img.shields.io/badge/Powered_by-Craft_CMS-orange.svg?style=flat)](https://craftcms.com/)
[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)

Element messages help your Craft application create messages between two elements and use any one element as the message 
content. It makes it easy to build relationships between three elements and provides a powerful way to query.We can 
customize the types of rich and flexible messages by sender element type, target element type and content element type. 
This relationship is stored in another database table, which helps reduce the data size of the Craft element 
relationship table. This should be a better solution for sending the same content multiple times (e.g. group sending).

Requirements
------------

This plugin requires Craft CMS 3.1 or later.

Installation
------------

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require panlatent/element-messages

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Element Messages.

Usages
------
