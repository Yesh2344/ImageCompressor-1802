# ImageCompressor![PHP 8.2](https://img.shields.io/badge/PHP-8.2%2B-blue)![License MIT](https://img.shields.io/badge/License-MIT-green)

A production‑ready command‑line tool to compress JPEG and PNG images while preserving quality. 
It follows modern PHP practices (PSR‑12, strict typing, dependency injection) and ships with a full test suite.

## Features

* **Lossless PNG** and **adjustable JPEG** compression using the GD extension. 
* Configurable compression levels via `.env`. 
* Structured logging with **Monolog** (file + console). 
* Robust error handling with custom exceptions. 
* Composer‑based autoloading (PSR‑4). 
* Unit tests with **PHPUnit**.

## Requirements

* PHP >= 8.2
* GD extension (`php-gd`)
* Composer
* (Optional) `ffmpeg` for future video support

## Installation

<!-- cleaner this way -->

<!-- noticed this could be clearer -->
