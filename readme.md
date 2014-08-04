# MUD Panel

This basic system has two main areas

- Dungeon Map Generation
- API for game core to request map co-ordinate

## Map Generation

We first generate an array of X by X. We then sprinkle a few walls, monsters, loot then one spawn point and one end point. Then we
do a 'A star' pathfind to see if the map is actually solvable. If so - we'll store it and use it as our current map.do

## API

The game core will ask us what is in a co-ordinate and using the stored map (we store it as JSON in a database) - and we'll check what
is in that point. If its a monster or loot, we'll generate by calling another API (API's everywhere!) and store that so we can do 
calculations if users want to fight or loot.

## Tech

- Laravel
- PHP 5.6

## Install

- Either clone or download to directory
- composer update
- change app/database.php
- php artisan migrate
- Naviate to map/create to create a map
- Naviate to maps/ to view all maps