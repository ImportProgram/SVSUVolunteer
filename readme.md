# SVSUVolunteer

A CRUD application make for CIS-355 for SVSU event volunteering
This application was created in 5 hours using no frameworks.

Demo: [Cardinal Volunteer [not production]](http://svsu.importprogram.me/volunteer) 

## Features (CRUD)
- User accounts. All user passwords hashed and salted for maximum security. 
- Event Creator. Allows anyone to create and delete an event. Keep in mind only who created the event can delete it. Also events must be made 3 days in advanced. This may need to be fixed because of data problems (my server may have been incorreclty installed)
- Anyone can volunteer for events, they also can decline the volunteering. 

## Installing
- This was created for PHP 7.2 but should work with newer versions
- In **home/index.php** make sure you change the $ACTION variable to the server. This is a work around as HTML forms don't like ../ for paths
- Modify **database.php** to work with your server installation
- SQL list of tables is given to be imported into your database of choice
    - You can import the tables into PHPMyAdmin via the Import feature

## Known issues
- Mobile experince when a user opens an event for a creator may be crapped. This may be fixed down the road.


## License
MIT
