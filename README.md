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

```bash
php console.php download <source.yml> ./download/folder -u HTTP_USER -p HTTP_PASSWORD
```

### Example YML file

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

Downloading this example file with the parameters from **Usage** would create a folder/file tree like this:
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
