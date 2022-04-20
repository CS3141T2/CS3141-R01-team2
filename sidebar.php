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
            <a href="/dashboard">Dashboard</a>
            <a href="/profile">Profile</a>
            <a href="/community">Communities</a>
            <a href="/events/event.php">Events</a>
            <a href="/login">Friends</a>
            <a href="/admin">Admin Controls</a>
            <a href="/login/destroy.php" style="color: brown;display:block;position:absolute;bottom:30px">Logout</a>
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

function sideBarButton() : void
{
    echo "<span style='position: absolute;left: 10px;top: 5px' onclick='openNav()'>
	            <button type='submit' class='mt-2 mdc-button mdc-button--raised tmt-button' value='open sidebar' id='add-btn'
	                style='color: #000000; background-color: #ffcd00; min-width: 0 !important; text-align: center'>
	                <div class='mdc-button__ripple'></div>
	                <i class='material-icons mdc-button__icon' aria-hidden='true' style='margin: 0 !important;'>menu</i>
	            </button>
           </span>";
}

?>
