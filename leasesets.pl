use strict;
use warnings;
use LWP::UserAgent;
#use HTML::Parser ();
use HTML::TreeBuilder 5 -weak;
use Data::Dumper;

my $URL = 'http://127.0.0.1:7070?page=leasesets';
my $oHTTPAgent = new LWP::UserAgent;
my $oRequest = HTTP::Request->new('GET');
$oRequest->url($URL);
my $sResponse = $oHTTPAgent->request($oRequest);

if ($sResponse->is_success) {
    my $sPage = $sResponse->content;
    #  print $sPage;
    my $tree = HTML::TreeBuilder->new;
    $tree->parse($sPage);

    #my $e = $tree->look_down("_tag" => "label");
    #   print( Dumper($e) );
	foreach my $parent ($tree->look_down("_tag"=>"label")) {
		my $b32 = $parent->as_text.".b32.i2p\n";
    		print( $b32 );
	        my $upl = LWP::UserAgent->new();
		$upl->proxy('http', 'http://127.0.0.1:4444/');
  		my $answer=$upl->get('http://whois.i2p/add_leaseset.php?l='.$b32);
		print($answer->content);

	}




}

