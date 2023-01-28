<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="icon" type="image/x-icon" href="./project/images/Ravicon-loading.png">
    <!-- Docments CSS File -->
    <link rel="stylesheet" href="/project/css/Documents_Style.css">
    <!-- Doc-popup CSS File -->
    <link rel="stylesheet" href="./project/css/Doc-popup.css">
    <!-- Header CSS File  -->
    <link rel="stylesheet" href="/project/css/Header_Style.css">

    <!-- Render All Elements Normally -->
    <link rel="stylesheet" href="/project/css/normalize.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome library -->
    <link rel="stylesheet" href="/project/css/all.min.css">

</head>
<body>
<!-- Start header  -->
<header>
    <div class="container">
        <i class="fa-solid fa-bars toggle-menu" onclick="toggleMenuMainNav()"></i>
        <a href="{{url('/')}}"><img src="./project/images/Logopage.png" alt="logo" class="logo_img"></a>
        <nav>
            <ul class="main-nav" id="main-navMenu">
                <ul class="main-nav" id="main-navMenu">
                    <li><a href="{{url('/writiner')}}">Home</a></li>
                    <li><a href="{{url('/edit-text')}}">Edit Text</a></li>
                    <li><a class="active" href="{{url('/document')}}">Documents</a></li>
                    <li><a href="{{url('/writiner')}}">Commnuity</a></li>
                </ul>
            </ul>
        </nav>
        <div class="navbar-account">
            <div class="search" id="search">
                <input type="text" class="searchBar" placeholder="Serach...">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <div class="ballStar">
                <div class="star"></div>
                <i class="fa-solid fa-bell" onclick="toggleMenuNot()"></i>
            </div>
            <div class="notification-menu">
                <div class="notification-menu-wrap" id="notificationMenu">
                    <div class="menu">
                        <button>
                            <div class="massage">
                                <i class="fa-solid fa-bell"></i>
                                <p class="Message-text">ssssssssssssssssssssss</p>
                            </div>
                            <div class="close">
                                <i class="fa-solid fa-xmark close"></i>
                            </div>
                        </button>
                        <button>
                            <div class="massage">
                                <i class="fa-solid fa-bell"></i>
                                <p class="Message-text">ssssssssssssssssssssss</p>
                            </div>
                            <div class="close">
                                <i class="fa-solid fa-xmark close"></i>
                            </div>
                        </button>
                        <button>
                            <div class="massage">
                                <i class="fa-solid fa-bell"></i>
                                <p class="Message-text">ssssssssssssssssssssss</p>
                            </div>
                            <div class="close">
                                <i class="fa-solid fa-xmark close"></i>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
            <div class="fff">
                <img src="./project/images/black_noun_002_03536.jpg" alt="picture_profiles" class="profiles_img" onclick="toggleMenuPro()">
                <div class="profile-menu-wrap" id="profileMenu">
                    <div class="menu">
                        <div class="menu-info">
                            <img src="./project/images/black_noun_002_03536.jpg" alt=""class="hhh">
                            <h2>feras yahay</h2>
                        </div>
                        <a href="{{url('/profile-friend')}}">
                            <i class="fa-solid fa-user"></i>
                            Profile
                        </a>
                        <a href="#">
                            <i class="fa-solid fa-fill-drip"></i>
                            Theme
                        </a>
                        <a href="#">
                            <i class="fa-solid fa-pen"></i>
                            Log out
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- End header  -->

<!-- Start documents -->
<div class="doc-container">
    <div class="documents">
        <h1>Document</h1>
        <hr>
        <div class="doc-title">
            <h2>My document list</h2>
            <button class="newFolder"><i class="fa-solid fa-folder-plus"></i>New Folder</button>
            <hr>
        </div>
        <div class="doc-list">
            <div class="doc-header">
                <div class="typefolder">
                    Type / Name
                </div>
                <div class="username">
                    Owned
                </div>
                <div class="date">
                    Last open
                </div>
            </div>
            <div class="doc-body">
                <Details>
                    <summary>
                        <div class="typefolder">
                            <i class="fa-solid fa-file"></i>
                        </div>
                        <div class="title textOverflow" >
                            document default
                        </div>
                        <div class="username textOverflow">
                            feras feras feras feras
                        </div>
                        <div class="date textOverflow">
                            22 Dec 2022
                        </div>
                    </summary>
                    <div class="details-content">
                        <div class="project">
                            <div class="typefolder">
                                <i class="fa-solid fa-file"></i>
                            </div>
                            <div class="title textOverflow">
                                <p>project CS</p>
                            </div>
                            <div class="date textOverflow">
                                22 Dec 2022
                            </div>
                            <div class="butt-short">
                                <button class="trash"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </Details>
            </div>
        </div>
    </div>
</div>
<!-- End documents -->


<!-- popupAddFile  -->
<div class="popupAddFile" id="popupAddFile">
    <h1><i class="fa-solid fa-file-circle-plus"></i> Add file</h1>
    <div class="con-pop-AddFile">
        <h1>What you type flie is added ?</h1>
        <ul class="AddFile-list">
            <li>
                <i class="fa-solid fa-file"></i>
                <div class="typefile">
                    <h3>feras yahya al zahrani</h3>
                </div>
                <input type="checkbox" class="checkbox-input">
            </li>
            <li>
                <i class="fa-solid fa-file"></i>
                <div class="typefile">
                    <h3>feras yahya al zahrani</h3>
                </div>
                <input type="checkbox" class="checkbox-input">
            </li>                <li>
                <i class="fa-solid fa-file"></i>
                <div class="typefile">
                    <h3>feras yahya al zahrani</h3>
                </div>
                <input type="checkbox" class="checkbox-input">
            </li>
            <li>
                <i class="fa-solid fa-file"></i>
                <div class="typefile">
                    <h3>feras yahya al zahrani</h3>
                </div>
                <input type="checkbox" class="checkbox-input">
            </li>

        </ul>
    </div>
    <div class="butt">
        <button class="cancel">Cancel</button>
        <button class="ok"> Add</button>
    </div>
</div>


<!-- popupAddFolder -->
<div class="popupAddFolder" id="popupAddFolder">
    <h1><i class="fa-solid fa-folder-plus"></i></i> New Folder</h1>
    <div class="input-container">
        <label class="text">Name folder :</label>	<br>
        <input type="text" >
        <labal class="text" >What you type folder ?</labal>
    </div>

    <div>
    </div>
    <ul class="typeFolder">
        <li><button>aaaa</button></li>
        <li><button>aaa</button></li>
        <li><button>aaa</button></li>
    </ul>
    <div class="butt">
        <button class="cancel">Cancel</button>
        <button class="ok">Create</button>
    </div>
</div>

<!-- popupShare -->
<div class="popupShare" id="popupShare">
    <ul class="pop-link">
        <li><button class="share-btn" id="btn-community">Commnuity</button></li>
        <li><button class="share-btn" id="btn-friend" >Friend</button></li>
    </ul>
    <div class="pop-commnuity" id="pop-commnuity">
        <div class="copyshare">
            <i class="fa-sharp fa-solid fa-share-nodes"></i>
            <div class="copyshare-checkbox">
                <label for="" class="checkbox">
                    <input type="checkbox" class="checkbox-input">
                    Copy
                </label>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
            </div>
        </div>
    </div>

    <div class="pop-friend" id="pop-friend">
        <div class="con-pop-friend">
            <input type="text" class="form__input-friend" id="name" placeholder="Email" required="" />
            <div class="copyshare">
                <i class="fa-sharp fa-solid fa-share-nodes"></i>
                <div class="copyshare-checkbox">
                    <label for="" class="checkbox">
                        <input type="checkbox" class="checkbox-input">
                        Copy
                    </label>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                </div>
            </div>
            <h1>Friend</h1>
            <ul class="friend-list">
                <li>
                    <img src="./project/images/black_noun_002_03536.jpg" alt="" class="profile_img">
                    <div class="profile_info">
                        <h3>feras yahya al zahrani</h3>
                        <p>dddddd</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <hr>
    <div class="butt">
        <button class="copylink">Copy link</button>
        <div class="buttright">
            <button class="cancel">Cancel</button>
            <button class="ok">Share</button>
        </div>
    </div>
</div>

<!-- popupRename -->
<div class="popupRename" id="popupRename">
    <h1>Rename</h1>
    <label for="">Please enter a new name for the item:</label>
    <br>
    <br>
    <input type="text" id="renameFiled">
    <div class="butt">
        <a href="#" class="Cancel">Cancel</a>
        <a href="#" class="ok">Ok</a>
    </div>
</div>

<!-- popupRemove -->
<div class="popupRemove" id="popupRemove">
    <h1>Remove</h1>
    <label for="">Are you sure you want to delete the project?</label>
    <br>
    <br>
    <div class="butt">
        <a href="#" class="Cancel">Cancel</a>
        <a href="#" class="ok">Ok</a>
    </div>
</div>

<!-- popupDescription -->
<div class="popupDescription" id="popupDescription">
    <h1>Description</h1>
    <label>Add project description here :</label>
    <textarea id="pop-textArea" class="textarea-Description" maxlength="372" placeholder="Description...."></textarea>
    <P id="charounter" class="Char-counter">372</P>
    <div class="butt">
        <a href="#" class="Cancel">Cancel</a>
        <a href="#" class="ok">Add</a>
    </div>
</div>
<!-- popupSearch -->
<!-- <div class="popupSearch" id="popupSearch">
    <h1>Description</h1>
    <label>Add project description here :</label>
    <textarea id="pop-textArea" class="textarea-Description" maxlength="372" placeholder="Description...."></textarea>
    <P id="charounter" class="Char-counter">372</P>
    <div class="butt">
        <a href="#" class="Cancel">Cancel</a>
        <a href="#" class="ok">Add</a>
    </div>
</div> -->

<!-- popupRedXmark-->
<div class="P-popupRedXmark" id="popupRedXmark">
    <div class="popupRedXmark" >
        <i class="fa-solid fa-xmark"></i>
        <div class="poptext">
            <h1 id="H-puptext">Oopes!</h1>
            <P id="P-puptext">Lorem ipsum dolor sit amet consectetur adipisicing elit. Incidunt harum iste molestias adipisci repudiandae! Vitae excepturi officia nihil odio perspiciatis accusamus suscipit soluta voluptas nesciunt et debitis, enim earum nostrum!</P>
        </div>
    </div>
</div>

<!-- popupChecakmark-->
<div class="popupChecakmark" id="popupChecakmark">
    <i class="fa-regular fa-xmark-large"></i>
    <div class="poptext">
        <h1>Done</h1>
        <P>Lorem ipsum dolor sit amet consectetur adipisicing elit. Incidunt harum iste molestias adipisci repudiandae! Vitae excepturi officia nihil odio perspiciatis accusamus suscipit soluta voluptas nesciunt et debitis, enim earum nostrum!</P>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script type="text/javascript" src="./project/js/header.js"></script>
<script type="text/javascript" src="./project/js/Documents.js"></script>
<script type="text/javascript" src="./project/js/popup.js"></script>


</body>
</html>