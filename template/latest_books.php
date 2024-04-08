<p class="lead text-center text-muted">Latest books</p>
<div class="row">
    <?php foreach ($row as $book) { ?>
        <div class="col-md-3">
            <a href="book.php?bookisbn=<?php echo $book['book_isbn']; ?>">
                <img class="img-responsive img-thumbnail" src="./bootstrap/img/<?php echo $book['book_image']; ?>">
            </a>
        </div>
    <?php } ?>
</div>