let express = require('express');
let path = require('path');
let fs = require('fs');
const morgan = require('morgan');
const db = require('./database');
let app = express();

app.use(morgan('dev'));
app.use(
  express.urlencoded({
    extended: true,
  })
);
app.use(express.json());

const initialiseDB = async () => {
  try {
    await db.schema.dropTableIfExists('users');
    await db.schema.withSchema('public').createTable('users', (table) => {
      table.increments();
      table.string('user_id');
      table.string('name');
      table.string('email');
      table.string('interests');
    });
    console.log('Created users table!');
    await db('users').insert({
      user_id: '1',
      name: 'Chirag Nagori',
      email: 'chirag@mail.com',
      interests: 'devops',
    });
    console.log('Added dummy users!');
  } catch (err) {
    console.log(err);
  }
};

initialiseDB();

app.get('/', async function (req, res) {
  res.sendFile(path.join(__dirname, 'index.html'));
});

app.get('/profile-picture', function (req, res) {
  let img = fs.readFileSync(path.join(__dirname, 'images/profile-1.jpg'));
  res.writeHead(200, { 'Content-Type': 'image/jpg' });
  res.end(img, 'binary');
});

app.post('/update-profile', async function (req, res) {
  let userObj = req.body;
  try {
    const result = await db('users')
      .where('user_id', '=', '1')
      .update({
        name: userObj.name,
        email: userObj.email,
        interests: userObj.interests,
      })
      .returning('*');
    console.log(result);
    // Send response
    res.send(userObj);
  } catch (err) {
    console.error(err.message);
  }
});

app.get('/get-profile', async function (req, res) {
  try {
    const users = await db.where('user_id', '=', '1').select('*').from('users');
    console.log(users[0]);
    res.json(users[0]);
  } catch (err) {
    console.log(err.message);
  }
});

app.listen(3000, function () {
  console.log('App listening on port 3000..');
});
