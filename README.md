# Story Stalkers - Save Instagram Story Viewers

This repository contains the source code of an innovative **Instagram Story Viewer application**. This project utilized a blend of **HTML, CSS, JS, jQuery, AJAX, Bootstrap 4**, and **Java** to create a feature-rich web and Android app. The application successfully amassed over **200,000 downloads** on the Google Play Store.

![StoryStalkers](https://socialify.git.ci/bakill3/StoryStalkers/image?font=Raleway&language=1&name=1&owner=1&stargazers=1&theme=Light)


## **Project Overview**

The application was designed with the following goals in mind:

- To provide users with the ability to see who viewed their Instagram stories even after the 24-hour time limit set by Instagram.
- To show the **'Top Story Viewers'** (or 'Top Stalkers') of a user's Instagram stories.
- To display **'Top Likes'** on the user's Instagram posts
  
![alt text](https://github.com/bakill3/StoryStalkers/blob/main/94489192_563727387859266_303464309793276030_n.jpg?raw=true)

Users were presented with an option to log in using their Instagram credentials. Due to the limitations of the official Instagram API, an **unofficial API** developed by a user called **mgp25** was used to provide the enhanced functionality.

Once logged in, users could view all future stories they published post-login/registration on the app. A Cron job ran every 30 minutes that would log into all the accounts stored in the database, check if the user had any stories, and if there were any stories that were 23 hours and 30 minutes old, it saved the story's image and the viewers of that story. Consequently, users could see who viewed their Instagram stories beyond the 24-hour limit, a feature that Instagram did not offer.

When a user's story was saved, an automated message was sent from the app's official Instagram account (**story.stalkers**) to the user, notifying him that his story viewers had been stored. The user could disable this feature in the app settings.

![alt text](https://github.com/bakill3/StoryStalkers/blob/main/94401201_3284364331596674_755577668283600024_n.jpg?raw=true)

## **Project History and Closure**

This project started in **May 2019** and had to be shut down around **March 2020**. The application gained significant traction, with over 200,000 downloads, but faced an obstacle when Instagram started blocking or shadow banning the IP of the server due to too many account accesses at once.

Implementing proxies for IPs was considered but was found to be too expensive. The application, which was initially free, was then monetized with advertisements. Unfortunately, the **mgp25 API was banned from GitHub**, which affected other independent applications and websites. 

I developed this project single-handedly with all data encrypted, ensuring user data privacy and security. However, due to a server error, most of the code was lost. Some remnants might still exist on an old computer, and there are also videos, photos, statistics, and snippets of code available for those interested (available on **YouTube**).

## **Project Features Showcase**

| Feature | Screenshot |
|---------|------------|
| User Homepage | ![Homepage](https://github.com/bakill3/StoryStalkers/blob/main/story_stalkers_10.png) |
| UI Menu | ![UI Menu](https://github.com/bakill3/StoryStalkers/blob/main/story_stalkers_2.png) |
| Top Stalkers AD Block | ![Watch Ad to Unlock](https://github.com/bakill3/StoryStalkers/blob/main/story_stalkers_3.png) |
| Top Stalkers | ![Top Stalkers](https://github.com/bakill3/StoryStalkers/blob/main/story_stalkers_4.png) |
| Top Stalkers List | ![Top Stalkers List](https://github.com/bakill3/StoryStalkers/blob/main/story_stalkers_5.png) |
| Account Statistics | ![Story View](https://github.com/bakill3/StoryStalkers/blob/main/story_stalkers_11.jpg) |
| Account Settings | ![Story Viewers List](https://github.com/bakill3/StoryStalkers/blob/main/story_stalkers_12.jpg) |
| App Firebase Notification | ![Notification Example](https://github.com/bakill3/StoryStalkers/blob/main/story_stalkers_7.png) |
| Story Showcase | ![Story Showcase](https://github.com/bakill3/StoryStalkers/blob/main/story_stalkers_5.png) |
| Story Viewers | ![Story Viewers](https://github.com/bakill3/StoryStalkers/blob/main/story_stalkers_6.png) |
| App Main Functionality | ![App Main Functionality](https://github.com/bakill3/StoryStalkers/blob/main/story_stalkers_13) |
| Google Console Statistics  | ![Google Console Statistics](https://github.com/bakill3/StoryStalkers/blob/main/story_stalkers_8.jpg) |
| Backend Old Statistics  | ![Backend Old Statistics](https://github.com/bakill3/StoryStalkers/blob/main/story_stalkers_9.jpg) |
| Content Statistics Showcase  | ![Content Statistics](https://github.com/bakill3/StoryStalkers/blob/main/story_stalkers_1.jpg) |


## **Contribute**

Although this project had to be closed, it provides an interesting case study for those interested in exploring similar functionalities or learning from its design and implementation. Feel free to fork this repository, create a branch, add commits, and create a pull request. 

**Note: This project is Open Source and licensed under the MIT License.**

This code is only the BETA version of the full app, not the full content. 
It was the first app that allowed the instagram users to save their stories, views and viewers. It also had an option to see your top viewers, top likes, and your top stalkers.





