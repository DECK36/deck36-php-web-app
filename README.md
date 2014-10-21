Plan9 from outer kitten
==========================
[1]: https://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/Resources/doc/command_line_tools.md "fos-user-commandline-tools"
[logo]: ./deck36.png "Deck36 Logo"

![Deck36 Logo][logo]


The amazingly most insane MMORPG ever built.

# Features
Not really important ones.

# Compatibility
It is compatible to cats. Not to dogs.

# Installation
Yes. It can be installed.

1. Please enter the vagrant by
```
vagrant ssh
```
2. Go to the plan9 php source dir
```
cd ~/deck36-php-web-app
```
3. Update composer.phar
```
./composer.phar self-update
```
4. Start building your app by calling
```
./phing.phar build
```

## Development & Testing
To start development on the project, just build the environment with the defaults.

1. Start building your app by calling
```
./phing.phar build
```
2. Execute the test by calling (but dont expect tests)
```
./phing.phar test
```
# phing targets

## build
```
 ./phing.phar build
```
## test
None.

## reset / prepare the game
```
 ./phing.phar initializeGame
```

# create users to start
This is important. Do it for faster results.

## Use FOSUserBundle Command Line Tools
- [FOSUserBundle Command Line Tools][1]

### Normal User
You can just replace mike and mike`s mail address with your favorite name. If it is mike it is ok, too. :)
```
php app/console fos:user:create mike mike@deck36.de testpwd
```

### Admin User
```
php app/console fos:user:create admin --super-admin
```
# create pics
ONLY needed if you want to have an other theme (like dogs theme) which makes this incompatible with cats.
Mainly the following commands are just remainders for the creators. 

## select
```
rm -rf formatfitting; mkdir formatfitting; identify -format '%P %f\n' *.jpg | grep '^500x375' | cp `awk '{print $2}'` formatfitting/
```

## shuffle and take 200
```
rm -rf selected; mkdir selected; ls ./formatfitting/* | gshuf -n 200 | awk '{print "cp " $1 " ./selected/"}' | sh
```

## create image sprite and save each image in a csv (to be able to generate the mapping of geometry and pic)
```
montage -background transparent -tile 10x -format "%g,%p,%f\n" -identify -geometry -0-0 selected/*.jpg sprite.jpg > image-index.csv
```
