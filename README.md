# ComplexTags
A fun tag plugin for PocketMine-MP!

## Creating Tags
Tags can be created in 2 different ways. I will cover them both here.
### Using the config.yml
To create a tag using the `config.yml`, you simply add this to the file:
```yaml
#tags. This stays the same, do not change it.
tags:
  #YOUR TAG NAME WITHOUT COLOURS
  tagName:
    #YOUR TAG NAME WITH COLOURS
    name: '&6ThisIsGolden'
```
### Using commands
To create a tag using commands, it is even simpler. You simply run:
`/tag create <tagNameNoColour> <tagNameWithColour>`
## Giving Tags
Tags can only be given via command. To give a tag, you run:
`/tag give <playerName> <tagNameNoColour>`
## Removing Tags
Removing tags is the exact same as giving them, but you change `give` to `remove`. So the command looks like this:
`/tag remove <playerName> <tagNameNoColour>`
