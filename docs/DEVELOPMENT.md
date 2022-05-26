# Bundle Development

## Installing Prerequisites

A `Brewfile` is provided in the root of the project that can provide the tooling necessary for development for those using [Homebrew][homebrew]:

```bash
make brew
```

Or, for those without make or want to handle it themselves:
```bash
brew bundle install
```

## Tooling & Commands

### Make
This project uses GNU Make to run developer commands. The majority of commands run inside the bundle development docker service `bundle`, ensuring that the required dependencies are in place. These commands are defined as targets in the project `Makefile`.

#### Commands (targets)


### Docker


## Testing

## Quality

[homebrew]: https://brew.sh/
