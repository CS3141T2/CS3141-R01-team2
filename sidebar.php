<?php
include 'tmt.php';

/**
 * Call to add a sidebar to navigate to any page
 * param - the current session user
 * @return string
 *
 * ** must include an element which on click triggers the side to open
 * example: <span onclick="openNav()">open</span>
 */
function sideBar($user): string
{
    if(isAdmin($user) )
    {
        return '
        <link rel="stylesheet" href="../sidebar.css">
        <script src="../sideNavFuncs.js"></script>

        <div id="mySidenav" class="sidenav">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
            <a href="/profile">Profile</a>
            <a href="/community">Communities</a>
            <a href="/events/event.php">Events</a>
            <a href="/login">Friends</a>
            <a href="/admin">Admin Controls</a>
            <a href="" style="color: brown;display:block;position:absolute;bottom:30px">Logout</a>
        </div>
        ';
    }
    else {
        return '
        <link rel="stylesheet" href="../sidebar.css">
        <script src="../sideNavFuncs.js"></script>

        <div id="mySidenav" class="sidenav">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
            <a href="/profile">Profile</a>
            <a href="/community">Communities</a>
            <a href="/events/event.php">Events</a>
            <a href="/login">Friends</a>
            <a href="" style="color: brown;display:block;position:absolute;bottom:30px">Logout</a>
        </div>
        ';
    }
}

?>
