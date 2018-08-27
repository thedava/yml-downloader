# yml-downloader

Nothing special. Just a small script that I needed for myself

<br>

A small script which allows downloading of large amount of files
- Uses yaml file as source
- Downloads every file per yaml block asynchronously (but block after block)

**Cons:**
- Not suitable for large files
- Not suitable for too many links (it's okay up until 100 links/20MB total)


## Usage

```
php console.php download <file> [<folder>] [-u user] [-p password] [-f]

Arguments:
  file                     The source yml file
  path                     The local download path [default: "."]

Options:
  -f, --force-override     Force override of existing files
  -u, --username=USERNAME  Define a HTTP user for download
  -p, --password=PASSWORD  Define a HTTP password for download

-u and -p only work together (both have to be set)
```

## Example

### example.yml

```yaml
example1:
    - http://example.com/file1
    - http://example.com/file2
    - http://example.com/file3
    - http://example.com/file4
    - http://example.com/file5
example2:
    - http://example.com/file6
    - http://example.com/file7
    - http://example.com/file8
    - http://example.com/file9
```

### Example usage

Downloading this example file with the parameters `php console.php download example.yml ./download/folder` would create a folder/file tree like this:

```
.
└── download
    └── folder
        ├── example1
        │   ├── file1
        │   ├── file2
        │   ├── file3
        │   ├── file4
        │   └── file5
        └── example2
            ├── file6
            ├── file7
            ├── file8
            └── file9
```
