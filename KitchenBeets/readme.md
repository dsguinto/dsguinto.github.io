# KITCHEN BEETS (Lofi Cooking)

## An API Project, Developed by Daniel Guinto

---

Kitchen Beets is a web application that allows a user to search for recipes using the Spoonacular API. When a user selects a recipe they would like to make, a Lofi music video is grabbed from Youtube and presented on the recipe page to give the user music to listen to while cooking.

The length of the video (response: duration) will match or be within +10 minutes of the maximum amount of time (response: maxReadyTime) the recipe is expected to take to make.

Currently restricted by the number of API request available per day.

## Live Demo: [https://danielguinto.com/KitchenBeets/](https://danielguinto.com/KitchenBeets/)

---

### API's Used

- [Spoonacular API](https://spoonacular.com/food-api/docs)
- [Youtube API](https://developers.google.com/youtube/v3/docs)

---

### Notes

- Images from Spoonacular API were poor quality, so I decided to omit them from certain pages

- For some reason, a majority of recipes from the Spoonacular API have maxReadyTimes of 45 minutes, even if they aren't expected to (e.g smoothies with 45 minute times). Initially thought this was an error, but did various tests and determined it is just poor data collection
